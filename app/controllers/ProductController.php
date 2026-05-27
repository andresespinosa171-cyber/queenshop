<?php

class ProductController extends Controller {
    private Product $product;

    public function __construct() {
        $this->product = new Product();
    }

    public function index(): void {
        $filters = [
            'search'      => $_GET['search'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'stock'       => $_GET['stock'] ?? '',
            'sort'        => $_GET['sort'] ?? 'name',
            'order'       => $_GET['order'] ?? 'ASC',
        ];

        $products   = $this->product->getAll($filters);
        $categories = $this->product->getAllCategories();

        $this->view('products/index', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters,
            'title'      => 'Productos',
        ]);
    }

    public function create(): void {
        $categories = $this->product->getAllCategories();
        $this->view('products/create', [
            'categories' => $categories,
            'title'      => 'Nuevo Producto',
        ]);
    }

    public function store(): void {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $purchase    = (float) ($_POST['purchase_price'] ?? 0);
        $sale        = (float) ($_POST['sale_price'] ?? 0);
        $stock       = (int) ($_POST['stock'] ?? 0);
        $categoryId  = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;

        if ($name === '') {
            session_flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('/products/create');
            return;
        }

        // Image upload
        $image = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $this->uploadImage($_FILES['image']);
        }

        $this->product->create([
            'name'           => $name,
            'description'    => $description,
            'purchase_price' => $purchase,
            'sale_price'     => $sale,
            'stock'          => $stock,
            'category_id'    => $categoryId,
            'image'          => $image,
        ]);

        session_flash('success', 'Producto creado correctamente.');
        $this->redirect('/products');
    }

    public function edit(int $id): void {
        $product = $this->product->findWithCategory($id);
        if (!$product) {
            session_flash('error', 'Producto no encontrado.');
            $this->redirect('/products');
            return;
        }

        $categories = $this->product->getAllCategories();
        $this->view('products/edit', [
            'product'    => $product,
            'categories' => $categories,
            'title'      => 'Editar Producto',
        ]);
    }

    public function update(int $id): void {
        $product = $this->product->find($id);
        if (!$product) {
            session_flash('error', 'Producto no encontrado.');
            $this->redirect('/products');
            return;
        }

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $purchase    = (float) ($_POST['purchase_price'] ?? 0);
        $sale        = (float) ($_POST['sale_price'] ?? 0);
        $stock       = (int) ($_POST['stock'] ?? 0);
        $categoryId  = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;

        if ($name === '') {
            session_flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect("/products/edit/{$id}");
            return;
        }

        $data = [
            'name'           => $name,
            'description'    => $description,
            'purchase_price' => $purchase,
            'sale_price'     => $sale,
            'stock'          => $stock,
            'category_id'    => $categoryId,
        ];

        // Image upload
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if ($product['image'] && file_exists(__DIR__ . '/../../' . $product['image'])) {
                unlink(__DIR__ . '/../../' . $product['image']);
            }
            $data['image'] = $this->uploadImage($_FILES['image']);
        }

        $this->product->update($id, $data);
        session_flash('success', 'Producto actualizado correctamente.');
        $this->redirect('/products');
    }

    public function destroy(int $id): void {
        $product = $this->product->find($id);
        if ($product) {
            // Delete image
            if ($product['image'] && file_exists(__DIR__ . '/../../' . $product['image'])) {
                unlink(__DIR__ . '/../../' . $product['image']);
            }
            $this->product->delete($id);
            session_flash('success', 'Producto eliminado.');
        }
        $this->redirect('/products');
    }

    public function restock(int $id): void {
        $product = $this->product->find($id);
        if (!$product) {
            session_flash('error', 'Producto no encontrado.');
            $this->redirect('/products');
            return;
        }

        $additional = (int) ($_POST['quantity'] ?? 0);
        if ($additional <= 0) {
            session_flash('error', 'La cantidad debe ser mayor a 0.');
            $this->redirect('/products');
            return;
        }

        $this->product->increaseStock($id, $additional);
        session_flash('success', "Stock actualizado: +{$additional} unidades de {$product['name']}.");
        $this->redirect('/products');
    }

    public function apiList(): void {
        $search = $_GET['q'] ?? '';
        $products = $this->product->apiSearch($search);
        $this->json($products);
    }

    private function uploadImage(array $file): string {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed, true)) {
            session_flash('error', 'Formato de imagen no permitido. Usá JPG, PNG, GIF o WebP.');
            $this->back();
            exit;
        }

        $filename = uniqid('prod_') . '.' . $ext;
        $dest = '/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $dest);
        return $dest;
    }
}
