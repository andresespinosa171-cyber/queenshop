<?php

class DashboardController extends Controller {
    private Sale $sale;
    private Product $product;

    public function __construct() {
        $this->sale    = new Sale();
        $this->product = new Product();
    }

    public function index(): void {
        $totalSales    = $this->sale->getTotalSales();
        $totalCost     = $this->sale->getTotalCost();
        $grossProfit   = $this->sale->getGrossProfit();
        $saleCount     = $this->sale->getSaleCount();
        $stockValue    = $this->product->getStockValue();
        $todaySales    = $this->sale->getTodaySales();
        $todayProfit   = $this->sale->getTodayProfit();
        $totalDiscount = $this->sale->getTotalDiscounts();
        $lowStock      = $this->product->getLowStock();
        $outOfStock    = $this->product->getOutOfStock();
        $recentSales   = $this->sale->getRecentSales(5);
        $salesByDay    = $this->sale->getSalesByDay(14);

        $profitMargin = $totalSales > 0
            ? round(($grossProfit / $totalSales) * 100, 1)
            : 0;

        $this->view('dashboard/index', [
            'totalSales'    => $totalSales,
            'totalCost'     => $totalCost,
            'grossProfit'   => $grossProfit,
            'profitMargin'  => $profitMargin,
            'saleCount'     => $saleCount,
            'stockValue'    => $stockValue,
            'todaySales'    => $todaySales,
            'todayProfit'   => $todayProfit,
            'totalDiscount' => $totalDiscount,
            'lowStock'      => $lowStock,
            'outOfStock'    => $outOfStock,
            'recentSales'   => $recentSales,
            'salesByDay'    => $salesByDay,
            'title'         => 'Dashboard',
        ]);
    }
}
