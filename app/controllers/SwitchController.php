<?php

class SwitchController extends Controller {
    private Company $company;

    public function __construct() {
        $this->company = new Company();
    }

    public function switch(int $companyId): void {
        // Validate company exists
        $company = $this->company->find($companyId);
        if (!$company) {
            session_flash('error', 'Tienda no encontrada.');
            $this->redirect('/');
            return;
        }

        // For now, allow switching if user is admin (user_id=1)
        // or if user has access via user_companies (future)
        $userId = $_SESSION['user_id'] ?? 0;

        if ($userId == 1) {
            // Admin can access all companies
        } else {
            // Check user_companies pivot
            $db = getDB();
            $access = $db->query(
                "SELECT 1 FROM user_companies WHERE user_id = ? AND company_id = ?",
                [$userId, $companyId]
            )->fetch();
            if (!$access) {
                session_flash('error', 'No tenés acceso a esa tienda.');
                $this->redirect('/');
                return;
            }
        }

        // Rebind session
        $_SESSION['company_id'] = (int) $company['id'];
        $_SESSION['company_name'] = $company['name'];
        $_SESSION['store_name'] = $company['store_name'] ?? $company['name'];
        $_SESSION['logo'] = $company['logo'] ?? 'logo.svg';
        $_SESSION['theme'] = $company['theme'] ?? 'queenshop';
        $_SESSION['primary_color'] = $company['primary_color'] ?? '#ffc107';
        $_SESSION['company_description'] = $company['description'] ?? '';

        session_flash('success', 'Cambiaste a ' . htmlspecialchars($_SESSION['store_name']));
        $this->redirect('/');
    }
}
