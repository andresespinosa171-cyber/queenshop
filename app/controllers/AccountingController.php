<?php

class AccountingController extends Controller {
    private Sale $sale;

    public function __construct() {
        $this->sale = new Sale();
    }

    public function index(): void {
        $companyId = current_company_id();
        $showAll = isset($_GET['all']) && $_GET['all'] === '1' && is_admin();
        $year = $_GET['year'] ?? date('Y');

        $stats = $this->sale->getMonthlyStats(
            $showAll ? null : $companyId,
            $year
        );

        // Calculate totals
        $totalSales = array_sum(array_column($stats, 'total_sales'));
        $totalCosts = array_sum(array_column($stats, 'total_cost'));
        $totalProfit = $totalSales - $totalCosts;
        $totalDiscounts = array_sum(array_column($stats, 'total_discounts'));

        $this->view('accounting/index', [
            'stats'          => $stats,
            'year'           => $year,
            'showAll'        => $showAll,
            'totalSales'     => $totalSales,
            'totalCosts'     => $totalCosts,
            'totalProfit'    => $totalProfit,
            'totalDiscounts' => $totalDiscounts,
            'title'          => 'Contabilidad',
        ]);
    }
}
