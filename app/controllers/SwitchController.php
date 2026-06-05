<?php

class SwitchController extends Controller {
    /**
     * Store switch now logs out and redirects to landing.
     * The user must re-authenticate to access a different store.
     */
    public function switch(int $id): void {
        $company = new Company();
        $store = $company->find($id);

        if ($store) {
            $storeName = $store['store_name'] ?? $store['name'];
            session_flash('info', 'Cambiaste a ' . htmlspecialchars($storeName) . '. Iniciá sesión para continuar.');
        }

        session_destroy();
        session_start();

        if ($store) {
            $_SESSION['store_preselected'] = [
                'company_id'    => (int) $store['id'],
                'name'          => $store['store_name'] ?? $store['name'],
                'theme'         => $store['theme'] ?? 'queenshop',
                'primary_color' => $store['primary_color'] ?? '#ffc107',
                'logo'          => $store['logo'] ?? 'logo.svg',
                'description'   => $store['description'] ?? '',
            ];
        }

        $this->redirect('/login');
    }
}
