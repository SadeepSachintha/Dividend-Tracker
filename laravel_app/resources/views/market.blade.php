<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Watch | Dividend Tracker - SS Labz</title>
    <!-- Favicon link -->
    <link rel="icon" type="image/png" href="/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['Space Grotesk', 'monospace'] 
                    },
                    colors: {
                        dark: '#080c14',
                        card: '#0e1422',
                        primary: '#00f2fe',
                        accent: '#0076ff',
                        purple: '#7f00ff',
                        success: '#34D399',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #080c14 0%, #0e1422 100%);
            color: #f8fafc;
            min-height: 100vh;
        }
        .glass {
            background: rgba(14, 20, 34, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 242, 254, 0.5);
            box-shadow: 0 10px 30px -10px rgba(0, 242, 254, 0.15);
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
            <div class="flex items-center gap-4">
                <a href="/" class="hover:opacity-90 transition-opacity">
                    <img src="/logo.png" alt="SS Labz Logo" class="w-12 h-12 rounded-xl object-contain shadow-lg shadow-primary/20">
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-[#00f2fe] via-[#0076ff] to-[#7f00ff] tracking-wide">
                        Market Watch
                    </h1>
                    <p class="text-slate-400 text-sm mt-1">Product of <a href="https://ss-labz.pages.dev/" target="_blank" class="text-primary hover:underline font-semibold font-mono">SS LABZ</a> | Explore all listed companies and their dividend status</p>
                </div>
            </div>
            <a href="/" class="glass px-6 py-2.5 rounded-xl font-semibold hover:bg-white/10 transition-all flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
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
                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-accent hover:opacity-90 text-slate-950 font-bold py-2.5 rounded-xl transition-all shadow-lg shadow-primary/20">
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
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all"></div>
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-bold text-primary uppercase tracking-wider">{{ $company->sector }}</span>
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

                    <button class="w-full bg-slate-800 hover:bg-slate-700 hover:border-primary/50 text-white text-sm font-bold py-2 rounded-lg transition-all border border-white/5">
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

        <!-- Footer -->
        <footer class="mt-20 border-t border-white/5 bg-[#080c14]/80 backdrop-blur-md py-6 px-6 rounded-2xl relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <img src="/logo.png" alt="SS Labz Logo" class="w-5 h-5 rounded-md opacity-80">
                    <span>&copy; {{ date('Y') }} <a href="https://ss-labz.pages.dev/" target="_blank" class="hover:text-primary transition-colors font-semibold">SS Labz</a>. All Rights Reserved.</span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Engineered by <a href="https://ss-labz.pages.dev/" target="_blank" class="text-slate-400 hover:text-primary transition-colors font-medium">Sadeep Sachintha</a></span>
                    <span class="text-slate-700">|</span>
                    <a href="https://ss-labz.pages.dev/#portfolio" target="_blank" class="hover:text-primary transition-colors">Showcase</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
