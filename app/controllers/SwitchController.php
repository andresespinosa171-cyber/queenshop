<?php

class SwitchController extends Controller {
    private Company $company;

    public function __construct() {
        $this->company = new Company();
    }

    public function switch(int $id): void {
        $companyId = $id;
        // Validate company exists
        $company = $this->company->find($companyId);
        if (!$company) {
            $this->showError('Tienda no encontrada.', $companyId);
            return;
        }

        // Check user access
        $userId = $_SESSION['user_id'] ?? 0;
        $hasAccess = false;

        if ($userId == 1) {
            $hasAccess = true; // Admin can access all
        } else {
            $db = getDB();
            $access = $db->prepare("SELECT 1 FROM user_companies WHERE user_id = ? AND company_id = ?");
            $access->execute([$userId, $companyId]);
            $hasAccess = (bool) $access->fetch();
        }

        if (!$hasAccess) {
            $this->showError('No tenés acceso a esa tienda.', $companyId);
            return;
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

    private function showError(string $message, int $failedId): void {
        $userId = $_SESSION['user_id'] ?? 0;

        // Get available stores for this user
        if ($userId == 1) {
            $available = $this->company->getAll();
        } else {
            $available = $this->company->getByUser($userId);
        }

        // If none found via getByUser, fallback to user's own company
        if (empty($available)) {
            $own = $this->company->find($_SESSION['company_id'] ?? 0);
            if ($own) $available = [$own];
        }

        $this->view('switch/error', [
            'error'           => $message,
            'availableStores' => $available,
            'title'           => 'Error al cambiar de tienda',
        ]);
    }
}
