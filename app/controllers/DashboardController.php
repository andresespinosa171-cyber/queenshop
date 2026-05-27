<?php

class DashboardController extends Controller {
    private Sale $sale;
    private Product $product;

    public function __construct() {
        $this->sale    = new Sale();
        $this->product = new Product();
    }

    public function index(): void {
        $companyId = current_company_id();
        $fortnightOnly = !isset($_GET['all']);

        $totalSales    = $this->sale->getTotalSales($companyId, $fortnightOnly);
        $totalCost     = $this->sale->getTotalCost($companyId, $fortnightOnly);
        $grossProfit   = $totalSales - $totalCost;
        $saleCount     = $this->sale->getSaleCount($companyId, $fortnightOnly);
        $stockValue    = $this->product->getStockValue($companyId);
        $todaySales    = $this->sale->getTodaySales($companyId);
        $todayProfit   = $this->sale->getTodayProfit($companyId);
        $totalDiscount = $this->sale->getTotalDiscounts($companyId, $fortnightOnly);
        $lowStock      = $this->product->getLowStock(companyId: $companyId);
        $outOfStock    = $this->product->getOutOfStock($companyId);
        $recentSales   = $this->sale->getRecentSales(5, $companyId, $fortnightOnly);
        $salesByDay    = $this->sale->getSalesByDay(14, $companyId, $fortnightOnly);

        $profitMargin = $totalSales > 0
            ? round(($grossProfit / $totalSales) * 100, 1)
            : 0;

        $currentFortnight = current_fortnight_range();

        $this->view('dashboard/index', [
            'totalSales'       => $totalSales,
            'totalCost'        => $totalCost,
            'grossProfit'      => $grossProfit,
            'profitMargin'     => $profitMargin,
            'saleCount'        => $saleCount,
            'stockValue'       => $stockValue,
            'todaySales'       => $todaySales,
            'todayProfit'      => $todayProfit,
            'totalDiscount'    => $totalDiscount,
            'lowStock'         => $lowStock,
            'outOfStock'       => $outOfStock,
            'recentSales'      => $recentSales,
            'salesByDay'       => $salesByDay,
            'fortnightOnly'    => $fortnightOnly,
            'currentFortnight' => $currentFortnight,
            'title'            => 'Dashboard',
        ]);
    }
}
