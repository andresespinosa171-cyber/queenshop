<?php

class Client extends Model {
    protected string $table = 'clients';

    public function getAllByCompany(?int $companyId = null, array $filters = []): array {
        $sql = "SELECT * FROM clients WHERE 1=1";
        $params = [];

        if ($companyId !== null) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR phone LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY name ASC";
        return $this->query($sql, $params)->fetchAll();
    }

    public function getDebtTotal(?int $companyId = null): float {
        $sql = "SELECT COALESCE(SUM(total_debt), 0) AS total FROM clients";
        $params = [];
        if ($companyId !== null) {
            $sql .= " WHERE company_id = ?";
            $params[] = $companyId;
        }
        $row = $this->query($sql, $params)->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function updateDebt(int $id, float $newDebt): void {
        $this->query(
            "UPDATE clients SET total_debt = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$newDebt, $id]
        );
    }

    public function adjustDebt(int $id, float $adjustment): void {
        $this->query(
            "UPDATE clients SET total_debt = total_debt + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$adjustment, $id]
        );
    }

    public function findWithCompanyCheck(int|string $id, int $companyId): array|false {
        return $this->query(
            "SELECT * FROM clients WHERE id = ? AND company_id = ?",
            [$id, $companyId]
        )->fetch();
    }
}
