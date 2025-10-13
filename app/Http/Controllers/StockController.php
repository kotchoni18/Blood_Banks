<?php

namespace App\Http\Controllers;

use App\Services\BloodStockService;

class StockController extends Controller
{
    protected $bloodStockService;

    public function __construct(BloodStockService $bloodStockService)
    {
        $this->bloodStockService = $bloodStockService;
    }

    public function index()
    {
        $stocks = $this->bloodStockService->getStocksByGroup();
        return view('admin.stocks.index', compact('stocks'));
    }

    public function critical()
    {
        $stocks = $this->bloodStockService->getCriticalStocks();
        return view('admin.stocks.critical', compact('stocks'));
    }

    public function expiring()
    {
        $stocks = $this->bloodStockService->getExpiringStocks();
        return view('admin.stocks.expiring', compact('stocks'));
    }
}
