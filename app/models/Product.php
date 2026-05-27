<?php

class Product extends Model {
    protected string $table = 'products';

    public function getAll(array $filters = [], ?int $companyId = null): array {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];

        // Company scoping
        if ($companyId !== null) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }

        // Search by name
        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE ?";
            $params[] = '%' . $filters['search'] . '%';
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int) $filters['category_id'];
        }

        // Stock filter
        if (!empty($filters['stock'])) {
            switch ($filters['stock']) {
                case 'out':
                    $sql .= " AND p.stock = 0";
                    break;
                case 'low':
                    $sql .= " AND p.stock > 0 AND p.stock <= 5";
                    break;
                case 'medium':
                    $sql .= " AND p.stock > 5 AND p.stock <= 20";
                    break;
                case 'high':
                    $sql .= " AND p.stock > 20";
                    break;
            }
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'p.name';
        $sortDir = strtoupper($filters['order'] ?? 'ASC');

        $allowedSorts = [
            'p.name', 'p.stock', 'p.sale_price', 'p.purchase_price',
            'p.created_at', 'category_name'
        ];
        // Map shorthand sort keys
        $sortMap = [
            'name'           => 'p.name',
            'stock'          => 'p.stock',
            'sale_price'     => 'p.sale_price',
            'purchase_price' => 'p.purchase_price',
            'created_at'     => 'p.created_at',
            'category'       => 'c.name',
        ];
        if (isset($sortMap[$sortField])) {
            $sortField = $sortMap[$sortField];
        }

        if (in_array($sortField, $allowedSorts, true)) {
            $dir = $sortDir === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$sortField} {$dir}";
        } else {
            $sql .= " ORDER BY p.name ASC";
        }

        return $this->query($sql, $params)->fetchAll();
    }

    public function findWithCategory(int|string $id): array|false {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ?",
            [$id]
        )->fetch();
    }

    public function getLowStock(int $threshold = 5, ?int $companyId = null): array {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.stock <= ? AND p.stock > 0";
        $params = [$threshold];

        if ($companyId !== null) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }

        $sql .= " ORDER BY p.stock ASC";
        return $this->query($sql, $params)->fetchAll();
    }

    public function getOutOfStock(?int $companyId = null): array {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.stock = 0";
        $params = [];

        if ($companyId !== null) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }

        $sql .= " ORDER BY p.name ASC";
        return $this->query($sql, $params)->fetchAll();
    }

    public function updateStock(int|string $id, int $quantity): void {
        $this->query(
            "UPDATE products SET stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$quantity, $id]
        );
    }

    public function decreaseStock(int|string $id, int $quantity): void {
        $this->query(
            "UPDATE products SET stock = stock - ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND stock >= ?",
            [$quantity, $id, $quantity]
        );
    }

    public function increaseStock(int|string $id, int $quantity): void {
        $this->query(
            "UPDATE products SET stock = stock + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$quantity, $id]
        );
    }

    public function getAllCategories(): array {
        return $this->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    }

    public function getStockValue(?int $companyId = null): float {
        $sql = "SELECT COALESCE(SUM(p.purchase_price * p.stock), 0) AS total FROM products p";
        $params = [];

        if ($companyId !== null) {
            $sql .= " WHERE p.company_id = ?";
            $params[] = $companyId;
        }

        $row = $this->query($sql, $params)->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function apiSearch(string $query, ?int $companyId = null): array {
        $sql = "SELECT id, name, sale_price, purchase_price, stock, image
                FROM products
                WHERE name LIKE ? AND stock > 0";
        $params = ['%' . $query . '%'];

        if ($companyId !== null) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }

        $sql .= " ORDER BY name ASC LIMIT 20";
        return $this->query($sql, $params)->fetchAll();
    }

    public function findWithCompanyCheck(int|string $id, int $companyId): array|false {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ? AND p.company_id = ?",
            [$id, $companyId]
        )->fetch();
    }
}
