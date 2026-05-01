<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Dividend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch holdings from DB
        $holdings = Holding::all();
        $isDemo = false;
        
        // Demo data fallback for presentation if DB is empty
        if ($holdings->isEmpty()) {
            $isDemo = true;
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
            $latestDividend = \App\Models\Dividend::where('symbol', $holding->symbol)
                                      ->orderBy('xd_date', 'desc')
                                      ->first();
            
            $dps = $latestDividend ? $latestDividend->amount_per_share : 0;
            
            // If it's demo mode and we have no DB data, inject some realistic numbers for display
            if ($isDemo && $dps == 0) {
                $mockDivs = ['COMB.N0000' => 5.00, 'JKH.N0000' => 4.50, 'HNB.N0000' => 8.00];
                $dps = $mockDivs[$holding->symbol] ?? 0;
            }

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
                'latest_xd' => $latestDividend ? $latestDividend->xd_date : ($isDemo ? '2026-05-15' : null),
            ];
        }

        $overallYieldOnCost = $totalInvestment > 0 ? ($estimatedAnnualIncome / $totalInvestment) * 100 : 0;

        // Fetch recent announcements
        $recentAnnouncements = \App\Models\Announcement::orderBy('date', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'holdingsData',
            'totalInvestment',
            'estimatedAnnualIncome',
            'overallYieldOnCost',
            'recentAnnouncements',
            'isDemo'
        ));
    }

    public function sync()
    {
        // Trigger the Python scraper
        $scraperPath = base_path('../scraper/scraper.py');
        $pythonPath = 'python'; // Hope it's in path for webserver
        
        // On Windows, sometimes we need the full path
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pythonPath = 'C:\\Users\\sadee\\AppData\\Local\\Python\\pythoncore-3.14-64\\python.exe';
        }

        $command = "\"$pythonPath\" \"$scraperPath\" --once 2>&1";
        $output = [];
        $resultCode = 0;
        
        exec($command, $output, $resultCode);

        return response()->json([
            'success' => $resultCode === 0,
            'message' => $resultCode === 0 ? 'Sync completed successfully!' : 'Sync failed.',
            'output' => $output
        ]);
    }
}
