<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Dividend;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('symbol', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Dividend Paying filter
        if ($request->has('dividend_only')) {
            $query->whereHas('dividends');
        }

        // Sector filter
        if ($request->has('sector') && $request->get('sector') != 'all') {
            $query->where('sector', $request->get('sector'));
        }

        $companies = $query->paginate(24);
        $sectors = Company::select('sector')->distinct()->whereNotNull('sector')->pluck('sector');

        return view('market', compact('companies', 'sectors'));
    }
}
