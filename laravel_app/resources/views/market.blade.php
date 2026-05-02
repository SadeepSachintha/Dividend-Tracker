<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Watch | Dividend Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
            min-height: 100vh;
        }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400">
                    Market Watch
                </h1>
                <p class="text-slate-400 mt-1">Explore all listed companies and their dividend status</p>
            </div>
            <a href="/" class="glass px-6 py-2.5 rounded-xl font-semibold hover:bg-white/10 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </header>

        <!-- Filters -->
        <div class="glass p-6 rounded-2xl mb-8 animate-fade-in">
            <form action="{{ route('market') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-400 mb-1">Search Company</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Symbol or Name..." 
                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Sector</label>
                    <select name="sector" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        <option value="all">All Sectors</option>
                        @foreach($sectors as $sector)
                            <option value="{{ $sector }}" {{ request('sector') == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-xl transition-all shadow-lg shadow-blue-900/20">
                        Filter Market
                    </button>
                </div>
                <div class="md:col-span-4 flex items-center gap-3 mt-2">
                    <input type="checkbox" name="dividend_only" id="dividend_only" {{ request('dividend_only') ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-white/10 bg-slate-900/50 text-blue-600 focus:ring-blue-500/50">
                    <label for="dividend_only" class="text-sm text-slate-300">Show only dividend-paying companies</label>
                </div>
            </form>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in" style="animation-delay: 0.1s">
            @forelse($companies as $company)
                <div class="glass p-5 rounded-2xl transition-all duration-300 card-hover group relative overflow-hidden">
                    <!-- Background Decoration -->
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">{{ $company->sector }}</span>
                            <h3 class="text-xl font-bold mt-1">{{ $company->symbol }}</h3>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-emerald-400">Rs. {{ number_format($company->last_price, 2) }}</div>
                            <div class="text-[10px] text-slate-500 uppercase">Last Traded</div>
                        </div>
                    </div>

                    <p class="text-slate-400 text-sm line-clamp-1 mb-6" title="{{ $company->name }}">{{ $company->name }}</p>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/5 p-3 rounded-xl">
                            <div class="text-[10px] text-slate-500 uppercase mb-1">Last Div</div>
                            <div class="text-sm font-semibold">
                                @if($company->last_dividend_amount)
                                    Rs. {{ number_format($company->last_dividend_amount, 2) }}
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <div class="text-[10px] text-slate-500 uppercase mb-1">Yield</div>
                            <div class="text-sm font-semibold text-emerald-400">
                                @if($company->last_dividend_amount && $company->last_price > 0)
                                    {{ number_format(($company->last_dividend_amount / $company->last_price) * 100, 2) }}%
                                @else
                                    <span class="text-slate-600">0.00%</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button class="w-full bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold py-2 rounded-lg transition-all border border-white/5">
                        Add to Tracker
                    </button>
                </div>
            @empty
                <div class="col-span-full py-20 text-center glass rounded-3xl">
                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xl text-slate-500">No companies found matching your criteria.</p>
                    <a href="{{ route('market') }}" class="text-blue-400 hover:underline mt-2 inline-block">Clear all filters</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12 mb-20">
            {{ $companies->links() }}
        </div>
    </div>
</body>
</html>
