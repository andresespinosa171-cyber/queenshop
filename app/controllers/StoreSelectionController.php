<?php

class StoreSelectionController extends Controller {
    private Company $company;

    public function __construct() {
        $this->company = new Company();
    }

    public function index(): void {
        // If already logged in, go to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        $stores = $this->company->getAll();

        $this->view('store-selection/index', [
            'stores' => $stores,
            'title'  => 'Elegí tu tienda',
        ]);
    }
}
