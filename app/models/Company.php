<?php

class Company extends Model {
    protected string $table = 'companies';

    public function getAll(): array {
        return $this->query("SELECT * FROM companies ORDER BY name")->fetchAll();
    }

    public function find(int|string $id): array|false {
        return $this->query("SELECT * FROM companies WHERE id = ?", [$id])->fetch();
    }

    /**
     * Get companies a user has access to via the user_companies pivot.
     */
    public function getByUser(int $userId): array {
        return $this->query(
            "SELECT c.*, uc.role AS access_role FROM companies c
             INNER JOIN user_companies uc ON uc.company_id = c.id AND uc.user_id = ?
             ORDER BY c.name",
            [$userId]
        )->fetchAll();
    }

    /**
     * Update branding fields for a company.
     */
    public function updateBranding(int|string $id, array $data): void {
        $allowed = ['theme', 'store_name', 'logo', 'primary_color', 'description'];
        $filtered = array_intersect_key($data, array_flip($allowed));

        if (empty($filtered)) {
            return;
        }

        $this->update($id, $filtered);
    }
}
