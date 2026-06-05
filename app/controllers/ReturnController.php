<?php

class ReturnController extends Controller {
    private ReturnModel $return;
    private Sale $sale;
    private Product $product;

    public function __construct() {
        $this->return  = new ReturnModel();
        $this->sale    = new Sale();
        $this->product = new Product();
    }

    public function index(): void {
        $returns = $this->return->getAllByCompany(current_company_id());
        $this->view('returns/index', [
            'returns' => $returns,
            'title'   => 'Devoluciones',
        ]);
    }

    public function create(): void {
        $saleData = null;

        // Search by sale ID
        $saleId = !empty($_GET['sale_id']) ? (int) $_GET['sale_id'] : null;

        // Search by client name
        $clientSearch = trim($_GET['client_search'] ?? '');

        $searchResults = [];
        if ($saleId) {
            $saleData = $this->sale->findWithItems($saleId);
            if (!$saleData || (isset($saleData['company_id']) && (int) $saleData['company_id'] !== current_company_id())) {
                $saleData = null;
                session_flash('error', 'Venta no encontrada.');
            }
        } elseif ($clientSearch !== '') {
            $searchResults = $this->sale->getByClientName($clientSearch, current_company_id());
        }

        $this->view('returns/create', [
            'saleData'      => $saleData,
            'searchResults' => $searchResults,
            'saleId'        => $saleId,
            'clientSearch'  => $clientSearch,
            'title'         => 'Nueva Devolución',
        ]);
    }

    public function store(): void {
        $saleId   = (int) ($_POST['sale_id'] ?? 0);
        $type     = $_POST['return_type'] ?? 'refund';
        $reasonRaw   = trim($_POST['reason'] ?? '');
        $reasonDetail = trim($_POST['reason_detail'] ?? '');
        $quantities = $_POST['qty'] ?? [];
        $actions    = $_POST['action'] ?? [];

        // Combine reason + detail
        $reason = $reasonRaw;
        if ($reasonDetail !== '') {
            $reason .= ': ' . $reasonDetail;
        }

        if ($saleId <= 0) {
            session_flash('error', 'Seleccioná una venta válida.');
            $this->redirect('/returns/create');
            return;
        }

        if ($reasonRaw === '') {
            session_flash('error', 'Indicá el motivo de la devolución.');
            $this->redirect("/returns/create?sale_id={$saleId}");
            return;
        }

        $saleData = $this->sale->findWithItems($saleId);
        if (!$saleData) {
            session_flash('error', 'Venta no encontrada.');
            $this->redirect('/returns/create');
            return;
        }

        // Build return items from selected quantities
        $items = [];
        $totalAmount = 0;

        foreach ($saleData['items'] as $item) {
            $pid = $item['product_id'];
            $qty = isset($quantities[$pid]) ? (int) $quantities[$pid] : 0;
            if ($qty <= 0) continue;

            $action = $actions[$pid] ?? 'restock';
            $subtotal = $qty * (float) $item['unit_price'];
            $totalAmount += $subtotal;

            $items[] = [
                'product_id'   => $pid,
                'product_name' => $item['product_name'],
                'quantity'     => $qty,
                'unit_price'   => (float) $item['unit_price'],
                'subtotal'     => $subtotal,
                'action'       => $action,
            ];
        }

        if (empty($items)) {
            session_flash('error', 'Seleccioná al menos un producto y cantidad a devolver.');
            $this->redirect("/returns/create?sale_id={$saleId}");
            return;
        }

        try {
            $this->return->createWithItems([
                'sale_id'      => $saleId,
                'company_id'   => current_company_id(),
                'return_type'  => $type,
                'reason'       => $reason,
                'total_amount' => $totalAmount,
            ], $items);

            // If refund, adjust client debt
            if ($type === 'refund' && !empty($saleData['client_id'])) {
                $clientModel = new Client();
                $clientModel->adjustDebt((int)$saleData['client_id'], -$totalAmount);
            }

            session_flash('success', 'Devolución registrada correctamente. Stock actualizado.');
            $this->redirect('/returns');
        } catch (Exception $e) {
            session_flash('error', 'Error al registrar la devolución: ' . $e->getMessage());
            $this->redirect("/returns/create?sale_id={$saleId}");
        }
    }

    public function show(int $id): void {
        $return = $this->return->findWithItems($id);
        if (!$return || (isset($return['company_id']) && (int) $return['company_id'] !== current_company_id())) {
            session_flash('error', 'Devolución no encontrada.');
            $this->redirect('/returns');
            return;
        }

        $this->view('returns/show', [
            'return' => $return,
            'title'  => 'Devolución #' . $id,
        ]);
    }
}
