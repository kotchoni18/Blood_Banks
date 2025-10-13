<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\blood_stocks;
use Carbon\Carbon;

class BloodStockService
{
    /**
     * Récupérer tous les stocks par groupe sanguin
     */
    public function getStocksByGroup()
    {
        return blood_stocks::select('blood_group', DB::raw('SUM(quantity_units) as total'))
            ->groupBy('blood_group')
            ->get();
    }

    /**
     * Récupérer le stock total
     */
    public function getTotalStock()
    {
        return blood_stocks::sum('quantity_units');
    }

    /**
     * Récupérer les stocks critiques (quantité < seuil)
     */
    public function getCriticalStocks($threshold = 10)
    {
        return blood_stocks::where('quantity_units', '<', $threshold)->get();
    }

    /**
     * Récupérer les stocks qui expirent bientôt
     */
    public function getExpiringStocks($days = 7)
    {
        $date = Carbon::now()->addDays($days);
        return blood_stocks::where('expiry_date', '<=', $date)->get();
    }

    /**
     * Ajouter une nouvelle poche de sang
     */
    public function addStock($data)
    {
        return blood_stocks::create($data);
    }
}
