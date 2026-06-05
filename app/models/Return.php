<?php

class ReturnModel extends Model {
    protected string $table = 'returns';

    public function getAllByCompany(?int $companyId = null): array {
        $sql = "SELECT r.*, s.final_total AS sale_total, c.name AS client_name
                FROM returns r
                JOIN sales s ON r.sale_id = s.id
                LEFT JOIN clients c ON s.client_id = c.id
                WHERE 1=1";
        $params = [];

        if ($companyId !== null) {
            $sql .= " AND r.company_id = ?";
            $params[] = $companyId;
        }

        $sql .= " ORDER BY r.created_at DESC";
        return $this->query($sql, $params)->fetchAll();
    }

    public function findWithItems(int|string $id): array|false {
        $return = $this->query(
            "SELECT r.*, s.final_total AS sale_total, s.client_id, c.name AS client_name
             FROM returns r
             JOIN sales s ON r.sale_id = s.id
             LEFT JOIN clients c ON s.client_id = c.id
             WHERE r.id = ?",
            [$id]
        )->fetch();

        if (!$return) return false;

        $return['items'] = $this->query(
            "SELECT * FROM return_items WHERE return_id = ? ORDER BY id ASC",
            [$id]
        )->fetchAll();

        return $return;
    }

    public function createWithItems(array $returnData, array $items): int {
        try {
            $this->db->beginTransaction();

            $returnId = $this->create($returnData);

            foreach ($items as $item) {
                $item['return_id'] = $returnId;
                $columns = implode(', ', array_keys($item));
                $placeholders = implode(', ', array_fill(0, count($item), '?'));
                $this->query(
                    "INSERT INTO return_items ({$columns}) VALUES ({$placeholders})",
                    array_values($item)
                );

                // Restock product if action is 'restock'
                if (($item['action'] ?? 'restock') === 'restock') {
                    $this->query(
                        "UPDATE products SET stock = stock + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                        [$item['quantity'], $item['product_id']]
                    );
                }
            }

            $this->db->commit();
            return $returnId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
