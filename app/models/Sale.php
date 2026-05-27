<?php

class Sale extends Model {
    protected string $table = 'sales';

    public function getAll(array $filters = []): array {
        $sql = "SELECT s.* FROM sales s WHERE 1=1";
        $params = [];

        // Date range
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(s.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(s.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY s.created_at DESC";

        return $this->query($sql, $params)->fetchAll();
    }

    public function findWithItems(int|string $id): array|false {
        $sale = $this->query(
            "SELECT * FROM sales WHERE id = ?",
            [$id]
        )->fetch();

        if (!$sale) return false;

        $items = $this->query(
            "SELECT si.*, p.image AS product_image
             FROM sale_items si
             LEFT JOIN products p ON si.product_id = p.id
             WHERE si.sale_id = ?
             ORDER BY si.id ASC",
            [$id]
        )->fetchAll();

        $sale['items'] = $items;
        return $sale;
    }

    /**
     * Create a sale with items in a transaction.
     *
     * @param array $saleData  ['total', 'discount_percent', 'discount_amount', 'final_total', 'item_count']
     * @param array $items     [['product_id', 'product_name', 'quantity', 'unit_price', 'purchase_price', 'subtotal'], ...]
     * @return int Sale ID
     */
    public function createWithItems(array $saleData, array $items): int {
        try {
            $this->db->beginTransaction();

            // Insert sale
            $saleId = $this->create($saleData);

            // Insert items
            foreach ($items as $item) {
                $item['sale_id'] = $saleId;
                $columns = implode(', ', array_keys($item));
                $placeholders = implode(', ', array_fill(0, count($item), '?'));
                $this->query(
                    "INSERT INTO sale_items ({$columns}) VALUES ({$placeholders})",
                    array_values($item)
                );

                // Decrease stock
                $this->query(
                    "UPDATE products SET stock = CASE WHEN stock >= ? THEN stock - ? ELSE 0 END, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                    [$item['quantity'], $item['quantity'], $item['product_id']]
                );
            }

            $this->db->commit();
            return $saleId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ─── Dashboard Stats ────────────────────────────────────────────

    public function getTodaySales(): float {
        $row = $this->query(
            "SELECT COALESCE(SUM(final_total), 0) AS total FROM sales WHERE DATE(created_at) = DATE('now')"
        )->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getTotalSales(): float {
        $row = $this->query("SELECT COALESCE(SUM(final_total), 0) AS total FROM sales")->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getTotalCost(): float {
        $row = $this->query(
            "SELECT COALESCE(SUM(si.purchase_price * si.quantity), 0) AS total FROM sale_items si"
        )->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getGrossProfit(): float {
        return $this->getTotalSales() - $this->getTotalCost();
    }

    public function getTodayProfit(): float {
        $sales = $this->query(
            "SELECT COALESCE(SUM(si.purchase_price * si.quantity), 0) AS cost
             FROM sale_items si
             JOIN sales s ON si.sale_id = s.id
             WHERE DATE(s.created_at) = DATE('now')"
        )->fetch();
        $cost = (float) ($sales['cost'] ?? 0);

        $todaySales = $this->getTodaySales();
        return $todaySales - $cost;
    }

    public function getTotalDiscounts(): float {
        $row = $this->query("SELECT COALESCE(SUM(discount_amount), 0) AS total FROM sales")->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getSaleCount(): int {
        $row = $this->query("SELECT COUNT(*) AS count FROM sales")->fetch();
        return (int) ($row['count'] ?? 0);
    }

    public function getRecentSales(int $limit = 10): array {
        return $this->query(
            "SELECT s.* FROM sales s ORDER BY s.created_at DESC LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function getSalesByDay(int $days = 30): array {
        return $this->query(
            "SELECT DATE(created_at) AS day,
                    COUNT(*) AS sale_count,
                    COALESCE(SUM(final_total), 0) AS total,
                    COALESCE(SUM(discount_amount), 0) AS discounts
             FROM sales
             WHERE created_at >= DATE('now', '-' || ? || ' days')
             GROUP BY DATE(created_at)
             ORDER BY day ASC",
            [$days]
        )->fetchAll();
    }
}
