<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Dividend;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // For now, we fetch all holdings since auth is not fully scaffolded.
        // Once Breeze is installed, this would be: auth()->user()->holdings
        $holdings = Holding::all();
        
        // Demo data fallback if database is empty
        if ($holdings->isEmpty()) {
            $holdings = collect([
                new Holding(['symbol' => 'COMB.N0000', 'quantity' => 1500, 'avg_price' => 95.50]),
                new Holding(['symbol' => 'JKH.N0000', 'quantity' => 2000, 'avg_price' => 180.25]),
                new Holding(['symbol' => 'HNB.N0000', 'quantity' => 800, 'avg_price' => 165.00]),
            ]);
        }
        
        $totalInvestment = 0;
        $estimatedAnnualIncome = 0;
        
        $holdingsData = [];

        foreach ($holdings as $holding) {
            $investment = $holding->quantity * $holding->avg_price;
            $totalInvestment += $investment;
            
            // Get latest dividend for this symbol
            $latestDividend = Dividend::where('symbol', $holding->symbol)
                                      ->orderBy('xd_date', 'desc')
                                      ->first();
            
            $dps = $latestDividend ? $latestDividend->amount_per_share : 0;
            $annualIncome = $holding->quantity * $dps;
            $estimatedAnnualIncome += $annualIncome;
            
            $yieldOnCost = $investment > 0 ? ($annualIncome / $investment) * 100 : 0;

            $holdingsData[] = [
                'symbol' => $holding->symbol,
                'quantity' => $holding->quantity,
                'avg_price' => $holding->avg_price,
                'investment' => $investment,
                'dps' => $dps,
                'annual_income' => $annualIncome,
                'yield_on_cost' => $yieldOnCost,
                'latest_xd' => $latestDividend ? $latestDividend->xd_date : null,
            ];
        }

        $overallYieldOnCost = $totalInvestment > 0 ? ($estimatedAnnualIncome / $totalInvestment) * 100 : 0;

        return view('dashboard', compact(
            'holdingsData',
            'totalInvestment',
            'estimatedAnnualIncome',
            'overallYieldOnCost'
        ));
    }
}
