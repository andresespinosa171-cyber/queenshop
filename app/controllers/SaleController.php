<?php

class SaleController extends Controller {
    private Sale $sale;
    private Product $product;

    public function __construct() {
        $this->sale    = new Sale();
        $this->product = new Product();
    }

    public function index(): void {
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
        ];

        $sales = $this->sale->getAll($filters, current_company_id());
        $this->view('sales/index', [
            'sales'   => $sales,
            'filters' => $filters,
            'title'   => 'Ventas',
        ]);
    }

    public function create(): void {
        $categories = $this->product->getAllCategories();

        // Get all clients for the selector
        $clientModel = new Client();
        $clients = $clientModel->getAllByCompany(current_company_id());

        $preselectedClient = !empty($_GET['client_id']) ? (int)$_GET['client_id'] : null;

        $this->view('sales/create', [
            'categories'         => $categories,
            'clients'            => $clients,
            'preselectedClient'  => $preselectedClient,
            'title'              => 'Nueva Venta',
        ]);
    }

    public function store(): void {
        $rawItems = json_decode($_POST['items'] ?? '[]', true);
        if (empty($rawItems)) {
            session_flash('error', 'Agregá al menos un producto a la venta.');
            $this->redirect('/sales/create');
            return;
        }

        $total           = (float) ($_POST['total'] ?? 0);
        $discountPercent = (float) ($_POST['discount_percent'] ?? 0);
        $discountAmount  = (float) ($_POST['discount_amount'] ?? 0);
        $finalTotal      = (float) ($_POST['final_total'] ?? 0);
        $itemCount       = 0;

        $clientId       = !empty($_POST['client_id']) ? (int) $_POST['client_id'] : null;
        $paymentStatus  = $_POST['payment_status'] ?? 'paid';
        $pendingAmount  = ($paymentStatus === 'pending' || $paymentStatus === 'partial') ? $finalTotal : 0;

        $items = [];
        foreach ($rawItems as $ri) {
            $qty = (int) ($ri['quantity'] ?? 1);
            if ($qty <= 0) continue;
            $itemCount += $qty;

            $items[] = [
                'product_id'     => (int) $ri['product_id'],
                'product_name'   => $ri['product_name'],
                'quantity'       => $qty,
                'unit_price'     => (float) ($ri['unit_price'] ?? 0),
                'purchase_price' => (float) ($ri['purchase_price'] ?? 0),
                'subtotal'       => (float) ($ri['subtotal'] ?? 0),
            ];
        }

        if (empty($items)) {
            session_flash('error', 'Agregá al menos un producto con cantidad válida.');
            $this->redirect('/sales/create');
            return;
        }

        try {
            $saleData = [
                'total'            => $total,
                'discount_percent' => $discountPercent,
                'discount_amount'  => $discountAmount,
                'final_total'      => $finalTotal,
                'item_count'       => $itemCount,
                'client_id'        => $clientId,
                'payment_status'   => $paymentStatus,
                'pending_amount'   => $pendingAmount,
            ];

            $saleId = $this->sale->createWithItems($saleData, $items, current_company_id());

            // Update client debt if pending
            if ($clientId && $pendingAmount > 0) {
                $clientModel = new Client();
                $clientModel->adjustDebt($clientId, $pendingAmount);
            }

            session_flash('success', 'Venta registrada correctamente.');
            $this->redirect('/sales');
        } catch (Exception $e) {
            session_flash('error', 'Error al registrar la venta: ' . $e->getMessage());
            $this->redirect('/sales/create');
        }
    }

    public function show(int $id): void {
        $sale = $this->sale->findWithItems($id);
        if (!$sale || (isset($sale['company_id']) && (int) $sale['company_id'] !== current_company_id())) {
            session_flash('error', 'Venta no encontrada.');
            $this->redirect('/sales');
            return;
        }

        // Get client info if set
        $client = null;
        if (!empty($sale['client_id'])) {
            $clientModel = new Client();
            $client = $clientModel->find($sale['client_id']);
        }

        $this->view('sales/show', [
            'sale'   => $sale,
            'client' => $client,
            'title'  => 'Venta #' . $id,
        ]);
    }

    public function apiProducts(): void {
        $categoryId = $_GET['category_id'] ?? '';
        $search     = $_GET['q'] ?? '';

        $filters = [];
        if ($categoryId) $filters['category_id'] = $categoryId;
        if ($search)     $filters['search'] = $search;
        $filters['sort'] = 'name';
        $filters['order'] = 'ASC';

        $products = $this->product->getAll($filters, current_company_id());
        $this->json($products);
    }
}
