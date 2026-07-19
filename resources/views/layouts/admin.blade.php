<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('hqShell', (cfg = {}) => ({
                inbox: cfg.inbox || [],
                aiUrl: cfg.aiUrl || null,
                openNav: false,
                openInbox: false,
                openCmd: false,
                cmdQuery: '',
                aiBusy: false,
                aiReply: null,
                get commands() { return window.hqCommands || []; },
                get isAiMode() {
                    const q = (this.cmdQuery || '').trim();
                    return !!this.aiUrl && (q.startsWith('>') || q.toLowerCase().startsWith('ai '));
                },
                aiPrompt() {
                    const q = (this.cmdQuery || '').trim();
                    if (q.startsWith('>')) return q.slice(1).trim();
                    if (q.toLowerCase().startsWith('ai ')) return q.slice(3).trim();
                    return q;
                },
                filtered() {
                    if (this.isAiMode) return [];
                    const q = (this.cmdQuery || '').trim().toLowerCase();
                    const list = this.commands;
                    if (!q) return list;
                    return list.filter((c) =>
                        c.title.toLowerCase().includes(q) ||
                        (c.subtitle && c.subtitle.toLowerCase().includes(q)) ||
                        (c.keywords && c.keywords.toLowerCase().includes(q))
                    );
                },
                go(url) {
                    this.openCmd = false;
                    window.location = url;
                },
                openCommandPalette() {
                    this.openInbox = false;
                    this.openNav = false;
                    this.openCmd = true;
                    this.aiReply = null;
                    this.$nextTick(() => this.$refs.cmdInput && this.$refs.cmdInput.focus());
                },
                async submitCmd() {
                    if (this.isAiMode) {
                        await this.runAi();
                        return;
                    }
                    const list = this.filtered();
                    if (list.length) this.go(list[0].url);
                },
                async runAi() {
                    const command = this.aiPrompt();
                    if (!command || !this.aiUrl || this.aiBusy) return;
                    this.aiBusy = true;
                    this.aiReply = null;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content;
                        const res = await fetch(this.aiUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ command }),
                        });
                        const data = await res.json().catch(() => ({}));
                        this.aiReply = data.message || data.error || (res.ok ? 'Done.' : 'AI request failed (' + res.status + ')');
                    } catch (e) {
                        this.aiReply = 'Could not reach AI command.';
                    } finally {
                        this.aiBusy = false;
                    }
                },
                onKey(e) {
                    const isCmdK = (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k';
                    if (isCmdK) {
                        e.preventDefault();
                        this.openCommandPalette();
                        return;
                    }
                    if (e.key === 'Escape') {
                        this.openCmd = false;
                        this.openInbox = false;
                        this.openNav = false;
                    }
                },
            }));
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Alpine is loaded via Vite (resources/js/app.js) --}}
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Compact HQ controls — fixed height, never stretch full-width */
        .hq-btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            height: 2rem;
            min-height: 2rem;
            max-height: 2rem;
            padding: 0 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1;
            border-radius: 0.5rem;
            white-space: nowrap;
            flex: 0 0 auto;
            width: auto !important;
            max-width: max-content;
            align-self: flex-end;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            box-sizing: border-box;
            transition: background-color .15s, color .15s, border-color .15s;
        }
        .hq-btn-primary { background: #2563eb; color: #fff; }
        .hq-btn-primary:hover { background: #1d4ed8; color: #fff; }
        .hq-btn-secondary { background: #1e293b; color: #fff; }
        .hq-btn-secondary:hover { background: #0f172a; color: #fff; }
        .hq-btn-ghost { background: #fff; color: #334155; border-color: #e2e8f0; }
        .hq-btn-ghost:hover { background: #f8fafc; }
        .hq-btn-amber { background: #d97706; color: #fff; }
        .hq-btn-amber:hover { background: #b45309; color: #fff; }
        .hq-btn-danger { background: #fff; color: #dc2626; border-color: #fecaca; }
        .hq-btn-danger:hover { background: #fef2f2; }
        .hq-field {
            height: 2rem;
            min-height: 2rem;
            padding: 0 0.625rem;
            font-size: 0.8125rem;
            line-height: 1.25;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-sizing: border-box;
        }
        .hq-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .hq-toolbar-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 0.5rem;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans"
      x-data="hqShell(@js([
          'inbox' => $hqInbox ?? [],
          'aiUrl' => auth()->user()?->hasRole('super-admin') ? route('ai.command') : null,
      ]))"
      @keydown.window="onKey">

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Nav Overlay + Drawer -->
        <div class="md:hidden">
            <div x-show="openNav" x-cloak x-transition.opacity class="fixed inset-0 bg-black/50 z-40" @click="openNav = false"></div>

            <aside x-show="openNav" x-cloak
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="-translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="-translate-x-full"
                   class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white z-50 flex flex-col">
                <div class="h-14 flex items-center justify-between px-4 border-b border-slate-800 flex-shrink-0">
                    <h1 class="text-lg font-bold tracking-wider"><span class="text-blue-500">AURATERIA</span> HQ</h1>
                    <button @click="openNav = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto py-3">
                    @include('layouts.partials.hq-nav')
                </nav>
                <div class="p-3 border-t border-slate-800 flex-shrink-0">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-400 hover:bg-slate-800 rounded-lg"><i class="fas fa-sign-out-alt w-5 text-center group-hover:text-red-500"></i> Logout</button>
                    </form>
                </div>
            </aside>
        </div>

        <!-- Desktop Sidebar -->
        <aside class="w-56 bg-slate-900 text-white hidden md:flex flex-col flex-shrink-0">
            <div class="h-14 flex items-center justify-center border-b border-slate-800 flex-shrink-0">
                <h1 class="text-lg font-bold tracking-wider"><span class="text-blue-500">AURATERIA</span> HQ</h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-3">
                @include('layouts.partials.hq-nav')
            </nav>

            <div class="p-3 border-t border-slate-800 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="group w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-400 hover:bg-slate-800 rounded-lg"><i class="fas fa-sign-out-alt w-5 text-center group-hover:text-red-500"></i> Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden relative">
            <header class="h-16 bg-white/90 backdrop-blur-sm shadow-sm flex items-center justify-between px-4 sm:px-6 z-10 border-b border-slate-200 sticky top-0">
                <div class="flex items-center gap-3">
                    <button @click="openNav = true" class="md:hidden text-slate-500 hover:text-slate-800 p-2 -ml-2">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight truncate">@yield('header')</h2>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Command Palette Trigger -->
                    <button @click="openCommandPalette()"
                            class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-sm text-slate-500 border border-slate-200 rounded-lg hover:border-slate-300 hover:text-slate-700 transition bg-slate-50">
                        <i class="fas fa-search text-xs"></i>
                        <span>Search</span>
                        <span class="text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded px-1.5 py-0.5 ml-1">⌘K</span>
                    </button>
                    <button @click="openCommandPalette()" class="sm:hidden text-slate-400 hover:text-blue-600 p-2 transition">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Notification Bell -->
                    <div class="relative">
                        <button @click="openInbox = !openInbox" class="relative p-2 text-slate-400 hover:text-blue-600 transition">
                            <i class="fas fa-bell"></i>
                            <span x-show="inbox.length > 0" x-cloak class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                        </button>

                        <div x-show="openInbox" x-cloak @click.outside="openInbox = false"
                             x-transition.opacity.duration.100ms
                             class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white rounded-xl shadow-lg border border-slate-200 z-30 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-700">Inbox</p>
                                <span class="text-[11px] text-slate-400" x-text="inbox.length + ' item' + (inbox.length === 1 ? '' : 's')"></span>
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                <template x-if="inbox.length === 0">
                                    <p class="px-4 py-6 text-sm text-slate-400 text-center">You're clear — no alerts</p>
                                </template>
                                <template x-for="item in inbox" :key="item.id">
                                    <a :href="item.url" class="block px-4 py-3 hover:bg-slate-50 transition">
                                        <p class="text-sm font-medium text-slate-700" x-text="item.title"></p>
                                        <p class="text-xs text-slate-400 mt-0.5" x-text="item.detail"></p>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <!-- Profile Link -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 hover:bg-slate-100 p-2 rounded-lg transition">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-slate-700 leading-tight">{{ Auth::user()->name }}</p>
                            @if(!Auth::user()->hasRole('employee'))
                            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">{{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}</p>
                            @endif
                        </div>
                        <div class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold shadow border-2 border-white flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </a>
                </div>
            </header>
            <div class="flex-1 overflow-auto p-4 sm:p-6 bg-slate-50">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border border-green-200 flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-green-900 hover:text-green-500 focus:outline-none"><i class="fas fa-times"></i></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm border border-red-200 flex justify-between items-center">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-900 hover:text-red-500 focus:outline-none"><i class="fas fa-times"></i></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Command Palette -->
    <div x-show="openCmd" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-24 px-4">
        <div x-show="openCmd" x-transition.opacity.duration.100ms class="fixed inset-0 bg-black/40" @click="openCmd = false"></div>

        <div x-show="openCmd"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.outside="openCmd = false"
             class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100">
                <i class="fas text-slate-400" :class="isAiMode ? 'fa-robot' : 'fa-search'"></i>
                <input type="text"
                       x-ref="cmdInput"
                       x-model="cmdQuery"
                       @keydown.enter.prevent="submitCmd()"
                       @keydown.escape="openCmd = false"
                       :placeholder="aiUrl ? 'Jump to… or type > create task …' : 'Jump to...'"
                       class="flex-1 border-0 focus:ring-0 text-sm text-slate-700 placeholder-slate-400 outline-none">
                <span class="text-[10px] font-semibold text-slate-400 bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5">ESC</span>
            </div>
            <div class="max-h-80 overflow-y-auto py-1">
                <template x-if="isAiMode">
                    <div class="px-4 py-4 space-y-3">
                        <p class="text-xs text-slate-500">AI command — press Enter to run</p>
                        <p class="text-sm text-slate-700" x-text="aiPrompt() || 'Type a command after >'"></p>
                        <button type="button" @click="runAi()" :disabled="aiBusy || !aiPrompt()"
                                class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 disabled:opacity-40">
                            <span x-show="!aiBusy">Run AI</span>
                            <span x-show="aiBusy" x-cloak>Working…</span>
                        </button>
                        <p x-show="aiReply" x-cloak class="text-sm text-slate-600 bg-slate-50 border border-slate-100 rounded-lg px-3 py-2" x-text="aiReply"></p>
                    </div>
                </template>
                <template x-if="!isAiMode && filtered().length === 0">
                    <p class="px-4 py-6 text-sm text-slate-400 text-center">No matching pages</p>
                </template>
                <template x-for="(cmd, idx) in filtered()" :key="cmd.title">
                    <button @click="go(cmd.url)"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-slate-50 transition">
                        <i class="fas" :class="cmd.icon" style="width:1.25rem; text-align:center; color:#64748b"></i>
                        <span class="flex-1">
                            <span class="block text-sm font-medium text-slate-700" x-text="cmd.title"></span>
                            <span class="block text-xs text-slate-400" x-text="cmd.subtitle"></span>
                        </span>
                        <i class="fas fa-arrow-turn-down text-[10px] text-slate-300"></i>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <script>
        window.hqCommands = [
            { title: 'Command Center', subtitle: 'Dashboard overview', url: '{{ route('dashboard') }}', icon: 'fa-th-large', keywords: 'dashboard home' },
            @role('super-admin')
            { title: 'My Day', subtitle: 'Today\'s top focuses', url: '{{ route('daily-focus.today') }}', icon: 'fa-sun', keywords: 'focus today' },
            @endrole
            @role(['super-admin', 'admin'])
            { title: 'My Tasks', subtitle: 'Personal task list', url: '{{ route('tasks.personal') }}', icon: 'fa-clipboard-check', keywords: 'tasks todo' },
            @endrole
            @can('view leads')
            { title: 'Pipeline', subtitle: 'Lead pipeline board', url: '{{ route('leads.pipeline') }}', icon: 'fa-columns', keywords: 'pipeline leads deals' },
            { title: 'All Leads', subtitle: 'Sales & CRM', url: '{{ route('leads.index') }}', icon: 'fa-user-plus', keywords: 'leads crm sales' },
            @endcan
            @can('view clients')
            { title: 'Clients', subtitle: 'Client accounts', url: '{{ route('clients.index') }}', icon: 'fa-building', keywords: 'clients accounts' },
            @endcan
            @can('view projects')
            { title: 'Projects', subtitle: 'Active projects', url: '{{ route('projects.index') }}', icon: 'fa-project-diagram', keywords: 'projects work' },
            @endcan
            @can('view invoices')
            { title: 'Invoices', subtitle: 'Billing & invoices', url: '{{ route('invoices.index') }}', icon: 'fa-file-invoice-dollar', keywords: 'invoices billing' },
            @endcan
            @can('view finance')
            { title: 'Finance', subtitle: 'Monthly P&L', url: '{{ route('finance.dashboard') }}', icon: 'fa-chart-pie', keywords: 'finance money pnl' },
            @endcan
            @role('super-admin')
            { title: 'Ventures', subtitle: 'All ventures', url: '{{ route('ventures.index') }}', icon: 'fa-rocket', keywords: 'ventures' },
            { title: 'New Venture', subtitle: 'Create a portfolio venture', url: '{{ route('ventures.create') }}', icon: 'fa-plus', keywords: 'venture create add' },
            { title: 'Content Topics', subtitle: 'LinkedIn / content idea pool', url: '{{ route('content-topics.index') }}', icon: 'fa-lightbulb', keywords: 'content topics marketing linkedin' },
            @endrole
            @if(auth()->user() && (auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports')))
            { title: 'Daily Reports', subtitle: 'Team daily reports', url: '{{ route('daily-reports.index') }}', icon: 'fa-clipboard-list', keywords: 'reports team' },
            @endif
            @role('super-admin')
            { title: 'Users', subtitle: 'User administration', url: '{{ route('users.index') }}', icon: 'fa-users', keywords: 'users admin' },
            @endrole
        ];

        // Commands list only — Alpine component lives in resources/js/app.js
    </script>
</body>
</html>
