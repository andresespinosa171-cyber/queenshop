<?php

class Sale extends Model {
    protected string $table = 'sales';
    private ?string $dbDriver = null;

    private function isMySQL(): bool {
        if ($this->dbDriver === null) {
            $this->dbDriver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        }
        return $this->dbDriver === 'mysql';
    }

    private function sqlNow(): string {
        return $this->isMySQL() ? 'CURDATE()' : "DATE('now')";
    }

    private function sqlSubDays(): string {
        return $this->isMySQL()
            ? 'DATE_SUB(CURDATE(), INTERVAL ? DAY)'
            : "DATE('now', '-' || ? || ' days')";
    }

    private function sqlMonth(): string {
        return $this->isMySQL()
            ? "DATE_FORMAT(s.created_at, '%m')"
            : "strftime('%m', s.created_at)";
    }

    private function sqlYear(): string {
        return $this->isMySQL()
            ? "DATE_FORMAT(s.created_at, '%Y')"
            : "strftime('%Y', s.created_at)";
    }

    public function getAll(array $filters = [], ?int $companyId = null): array {
        $sql = "SELECT s.* FROM sales s WHERE 1=1";
        $params = [];

        // Company scoping
        if ($companyId !== null) {
            $sql .= " AND s.company_id = ?";
            $params[] = $companyId;
        }

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
    public function createWithItems(array $saleData, array $items, ?int $companyId = null): int {
        try {
            $this->db->beginTransaction();

            // Add company_id if provided
            if ($companyId !== null) {
                $saleData['company_id'] = $companyId;
            }

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

    public function getTodaySales(?int $companyId = null, ?bool $fortnightOnly = false): float {
        $now = $this->sqlNow();
        $sql = "SELECT COALESCE(SUM(s.final_total), 0) AS total FROM sales s WHERE DATE(s.created_at) = {$now}";
        $params = [];

        if ($companyId !== null) {
            $sql .= " AND s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $row = $this->query($sql, $params)->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getTotalSales(?int $companyId = null, ?bool $fortnightOnly = false): float {
        $sql = "SELECT COALESCE(SUM(s.final_total), 0) AS total FROM sales s";
        $params = [];

        if ($companyId !== null) {
            $sql .= " WHERE s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            if ($companyId === null) {
                $sql .= " WHERE 1=1";
            }
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $row = $this->query($sql, $params)->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getTotalCost(?int $companyId = null, ?bool $fortnightOnly = false): float {
        $sql = "SELECT COALESCE(SUM(si.purchase_price * si.quantity), 0) AS total
                FROM sale_items si
                JOIN sales s ON si.sale_id = s.id";
        $params = [];
        $hasWhere = false;

        if ($companyId !== null) {
            $sql .= " AND s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            $sql .= " WHERE 1=1";
            $hasWhere = true;
            // Re-add company_id as WHERE clause if set (moved from JOIN AND)
            if ($companyId !== null) {
                $sql .= " AND s.company_id = ?";
                $params[] = $companyId;
            }
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $row = $this->query($sql, $params)->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getGrossProfit(?int $companyId = null): float {
        return $this->getTotalSales($companyId) - $this->getTotalCost($companyId);
    }

    public function getTodayProfit(?int $companyId = null, ?bool $fortnightOnly = false): float {
        $sql = "SELECT COALESCE(SUM(si.purchase_price * si.quantity), 0) AS cost
                FROM sale_items si
                JOIN sales s ON si.sale_id = s.id";
        $params = [];

        if ($companyId !== null) {
            $sql .= " AND s.company_id = ?";
            $params[] = $companyId;
        }

        $now = $this->sqlNow();
        $sql .= " WHERE DATE(s.created_at) = {$now}";

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $sales = $this->query($sql, $params)->fetch();
        $cost = (float) ($sales['cost'] ?? 0);

        $todaySales = $this->getTodaySales($companyId);
        return $todaySales - $cost;
    }

    public function getTotalDiscounts(?int $companyId = null, ?bool $fortnightOnly = false): float {
        $sql = "SELECT COALESCE(SUM(s.discount_amount), 0) AS total FROM sales s";
        $params = [];

        if ($companyId !== null) {
            $sql .= " WHERE s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            if ($companyId === null) {
                $sql .= " WHERE 1=1";
            }
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $row = $this->query($sql, $params)->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getSaleCount(?int $companyId = null, ?bool $fortnightOnly = false): int {
        $sql = "SELECT COUNT(*) AS count FROM sales s";
        $params = [];

        if ($companyId !== null) {
            $sql .= " WHERE s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            if ($companyId === null) {
                $sql .= " WHERE 1=1";
            }
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $row = $this->query($sql, $params)->fetch();
        return (int) ($row['count'] ?? 0);
    }

    public function getRecentSales(int $limit = 10, ?int $companyId = null, ?bool $fortnightOnly = false): array {
        $sql = "SELECT s.* FROM sales s";
        $params = [];

        if ($companyId !== null) {
            $sql .= " WHERE s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            if ($companyId === null) {
                $sql .= " WHERE 1=1";
            }
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $sql .= " ORDER BY s.created_at DESC LIMIT ?";
        $params[] = $limit;

        return $this->query($sql, $params)->fetchAll();
    }

    public function getSalesByDay(int $days = 30, ?int $companyId = null, ?bool $fortnightOnly = false): array {
        $since = $this->sqlSubDays();
        $sql = "SELECT DATE(s.created_at) AS day,
                       COUNT(*) AS sale_count,
                       COALESCE(SUM(s.final_total), 0) AS total,
                       COALESCE(SUM(s.discount_amount), 0) AS discounts
                FROM sales s
                WHERE s.created_at >= {$since}";
        $params = [$days];

        if ($companyId !== null) {
            $sql .= " AND s.company_id = ?";
            $params[] = $companyId;
        }

        if ($fortnightOnly) {
            $range = current_fortnight_range();
            $sql .= " AND DATE(s.created_at) BETWEEN ? AND ?";
            $params[] = $range['start'];
            $params[] = $range['end'];
        }

        $sql .= " GROUP BY DATE(s.created_at)
                  ORDER BY day ASC";

        return $this->query($sql, $params)->fetchAll();
    }

    // ─── Accounting Stats ───────────────────────────────────────────

    public function getMonthlyStats(?int $companyId = null, ?string $year = null): array {
        $year = $year ?? date('Y');
        $monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $monthExpr = $this->sqlMonth();
        $yearExpr = $this->sqlYear();
        $sql = "SELECT
                    {$monthExpr} AS month,
                    COUNT(*) AS sale_count,
                    COALESCE(SUM(s.final_total), 0) AS total_sales,
                    COALESCE(SUM(s.discount_amount), 0) AS total_discounts,
                    COALESCE(SUM(si.purchase_price * si.quantity), 0) AS total_cost
                FROM sales s
                LEFT JOIN sale_items si ON si.sale_id = s.id
                WHERE {$yearExpr} = ?";
        $params = [$year];

        if ($companyId !== null) {
            $sql .= " AND s.company_id = ?";
            $params[] = $companyId;
        }

        $sql .= " GROUP BY " . $this->sqlMonth() . " ORDER BY month ASC";

        $rows = $this->query($sql, $params)->fetchAll();

        // Ensure all 12 months are present
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthKey = sprintf('%02d', $m);
            $found = false;
            foreach ($rows as $row) {
                if ($row['month'] === $monthKey) {
                    $row['month_name'] = $monthNames[$m - 1];
                    $row['gross_profit'] = (float)$row['total_sales'] - (float)$row['total_cost'];
                    $result[] = $row;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $result[] = [
                    'month' => $monthKey,
                    'month_name' => $monthNames[$m - 1],
                    'sale_count' => 0,
                    'total_sales' => 0,
                    'total_discounts' => 0,
                    'total_cost' => 0,
                    'gross_profit' => 0,
                ];
            }
        }
        return $result;
    }
}
