<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dividend Tracker | Precision Yields</title>
    <!-- Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (CDN for zero-touch deployment) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        dark: '#0B0F19',
                        card: '#151A2D',
                        primary: '#38BDF8',
                        accent: '#818CF8',
                        success: '#34D399',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0B0F19; color: #F1F5F9; }
        .glass-panel {
            background: rgba(21, 26, 45, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .gradient-text {
            background: linear-gradient(to right, #38BDF8, #818CF8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(56, 189, 248, 0.2); }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0B0F19; }
        ::-webkit-scrollbar-thumb { background: #1E293B; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans relative overflow-x-hidden" x-data="{ notifications: [] }">

    <!-- Background glow effects -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-primary/20 rounded-full mix-blend-screen filter blur-[100px] opacity-50 -z-10 pointer-events-none"></div>
    <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-accent/20 rounded-full mix-blend-screen filter blur-[100px] opacity-50 -z-10 pointer-events-none"></div>

    <!-- Navigation -->
    <nav class="glass-panel sticky top-0 z-50 px-6 py-4 border-b border-white/5">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-accent flex items-center justify-center shadow-lg shadow-primary/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h1 class="text-xl font-bold tracking-wide">Div<span class="gradient-text">Tracker</span></h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative cursor-pointer hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span x-show="notifications.length > 0" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                </div>
                <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center">
                    <span class="text-sm font-medium">ME</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-10 space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h2 class="text-3xl font-bold">Portfolio Overview</h2>
                <p class="text-slate-400 mt-1">Real-time precision tracking of your corporate actions.</p>
            </div>
            <button 
                id="sync-btn"
                @click="syncing = true; 
                        fetch('{{ route('sync') }}', { 
                            method: 'POST', 
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
                        })
                        .then(r => r.json())
                        .then(data => {
                            syncing = false;
                            const event = new CustomEvent('notify', { 
                                detail: { title: data.success ? 'Success! 🚀' : 'Oops!', message: data.message } 
                            });
                            window.dispatchEvent(event);
                            if(data.success) setTimeout(() => location.reload(), 1500);
                        })
                        .catch(e => { syncing = false; alert('Sync error'); })"
                :disabled="syncing"
                class="bg-white/5 hover:bg-white/10 border border-white/10 px-5 py-2.5 rounded-xl text-sm font-medium transition-all hover-lift flex items-center gap-2 disabled:opacity-50"
                x-data="{ syncing: false }">
                <template x-if="!syncing">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </template>
                <template x-if="syncing">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </template>
                <span x-text="syncing ? 'Syncing...' : 'Sync Holdings'"></span>
            </button>
        </div>

        <!-- Demo Mode Banner -->
        @if($isDemo)
        <div class="glass-panel p-4 rounded-xl border-amber-500/20 bg-amber-500/5 flex items-center gap-4 text-amber-200">
            <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-sm">Demo Mode Active</p>
                <p class="text-xs opacity-75">Your database is currently empty. Showing simulated data for demonstration.</p>
            </div>
            <div class="flex-grow"></div>
            <button onclick="document.getElementById('sync-btn').click()" class="text-xs bg-amber-500/20 hover:bg-amber-500/30 px-3 py-1.5 rounded-lg transition-colors border border-amber-500/30">
                Sync Now
            </button>
        </div>
        @endif

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="glass-panel p-6 rounded-2xl hover-lift relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-sm font-medium text-slate-400">Total Investment</p>
                <h3 class="text-3xl font-bold mt-2">Rs. {{ number_format($totalInvestment, 2) }}</h3>
            </div>
            
            <!-- Card 2 -->
            <div class="glass-panel p-6 rounded-2xl hover-lift relative overflow-hidden group border-primary/20">
                <div class="absolute inset-0 bg-gradient-to-br from-success/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute top-6 right-6 w-10 h-10 bg-success/10 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <p class="text-sm font-medium text-slate-400">Estimated Annual Income</p>
                <h3 class="text-3xl font-bold mt-2 text-white">Rs. {{ number_format($estimatedAnnualIncome, 2) }}</h3>
                <p class="text-xs text-success mt-2 flex items-center gap-1 font-medium">
                    + Projected forward 12M
                </p>
            </div>

            <!-- Card 3 -->
            <div class="glass-panel p-6 rounded-2xl hover-lift relative overflow-hidden group border-accent/20">
                <div class="absolute inset-0 bg-gradient-to-br from-accent/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-sm font-medium text-slate-400">Yield on Cost (YoC)</p>
                <h3 class="text-3xl font-bold mt-2 gradient-text">{{ number_format($overallYieldOnCost, 2) }}%</h3>
                <div class="w-full bg-slate-800 rounded-full h-1.5 mt-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-accent h-1.5 rounded-full" style="width: {{ min(100, $overallYieldOnCost * 10) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
            
            <!-- Main Table (Left 2/3) -->
            <div class="lg:col-span-2 glass-panel rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-white/5 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Your Holdings & Dividends</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/5 text-slate-300 text-sm uppercase tracking-wider">
                                <th class="p-4 font-medium pl-6">Symbol</th>
                                <th class="p-4 font-medium text-right">Units Held</th>
                                <th class="p-4 font-medium text-right">Avg Price</th>
                                <th class="p-4 font-medium text-right">DPS (Latest)</th>
                                <th class="p-4 font-medium text-right">Income</th>
                                <th class="p-4 font-medium text-right pr-6">Yield (YoC)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            @forelse($holdingsData as $holding)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="p-4 pl-6 font-semibold flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-slate-800 flex items-center justify-center text-xs border border-slate-700 text-slate-300 group-hover:border-primary/50 transition-colors">
                                        {{ substr($holding['symbol'], 0, 1) }}
                                    </div>
                                    {{ $holding['symbol'] }}
                                    @if($holding['latest_xd'])
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-primary/10 text-primary border border-primary/20">XD: {{ $holding['latest_xd'] }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right font-medium">{{ number_format($holding['quantity']) }}</td>
                                <td class="p-4 text-right text-slate-400">{{ number_format($holding['avg_price'], 2) }}</td>
                                <td class="p-4 text-right text-success font-medium">{{ number_format($holding['dps'], 2) }}</td>
                                <td class="p-4 text-right text-white">{{ number_format($holding['annual_income'], 2) }}</td>
                                <td class="p-4 text-right pr-6">
                                    <span class="bg-white/10 px-2.5 py-1 rounded-lg border border-white/5">{{ number_format($holding['yield_on_cost'], 2) }}%</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    No holdings found. Sync your portfolio to begin.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Announcements (Right 1/3) -->
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-white/5 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Recent Corporate Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($recentAnnouncements as $ann)
                    <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-primary/20 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold text-primary uppercase tracking-tighter">{{ $ann->symbol }}</span>
                            <span class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($ann->date)->format('d M') }}</span>
                        </div>
                        <p class="text-xs font-medium text-slate-200 line-clamp-2 group-hover:text-white transition-colors">
                            {{ $ann->raw_text }}
                        </p>
                    </div>
                    @empty
                    <div class="py-10 text-center text-slate-500">
                        <p class="text-sm">No recent actions found.</p>
                        <p class="text-[10px] opacity-50 mt-1">Run sync to fetch latest data.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </main>

    <!-- Real-time Notifications Toast Overlay (Alpine.js) -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
        <template x-for="(note, index) in notifications" :key="index">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="glass-panel border-l-4 border-l-primary p-4 rounded-xl shadow-2xl flex items-start gap-4 min-w-[320px] max-w-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-transparent opacity-50 pointer-events-none"></div>
                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-sm text-white" x-text="note.title"></h4>
                    <p class="text-xs text-slate-300 mt-1" x-text="note.message"></p>
                </div>
                <button @click="notifications.splice(index, 1)" class="text-slate-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Script to simulate WebSockets (Since Reverb isn't running locally yet) -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Mock WebSocket connection
            setTimeout(() => {
                const event = new CustomEvent('notify', {
                    detail: {
                        title: 'New Dividend Announced! 🚀',
                        message: 'COMB just announced a Final Dividend of Rs. 5.00 per share. XD: 2026-05-10.'
                    }
                });
                window.dispatchEvent(event);
            }, 3000);
            
            // Laravel Echo integration placeholder
            /*
            window.Echo.channel('dividend-alerts')
                .listen('.dividend.received', (e) => {
                    const event = new CustomEvent('notify', {
                        detail: {
                            title: 'New Dividend Announced! 🚀',
                            message: e.message
                        }
                    });
                    window.dispatchEvent(event);
                });
            */
        });

        // Listen for internal notification events
        window.addEventListener('notify', (e) => {
            const data = document.querySelector('[x-data]').__x.$data;
            data.notifications.push(e.detail);
            
            // Auto dismiss
            setTimeout(() => {
                if(data.notifications.length > 0) {
                    data.notifications.shift();
                }
            }, 6000);
        });
    </script>
</body>
</html>
