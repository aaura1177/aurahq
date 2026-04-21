<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="//[unpkg.com/alpinejs](https://unpkg.com/alpinejs)" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col flex-shrink-0">
            <div class="h-16 flex items-center justify-center border-b border-slate-800">
                <h1 class="text-xl font-bold tracking-wider"><span class="text-blue-500">AURATERIA</span> HQ</h1>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-2">

                    {{-- Command Center --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-th-large w-5 text-center {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'group-hover:text-blue-400' }}"></i>
                            Command Center
                        </a>
                    </li>

                    {{-- My Work (Super-Admin Only) --}}
                    @role('super-admin')
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">My Work</li>

                    <li>
                        <a href="{{ route('daily-focus.today') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('daily-focus.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-sun w-5 text-center {{ request()->routeIs('daily-focus.*') ? 'text-amber-400' : 'group-hover:text-amber-400' }}"></i>
                            My Day
                        </a>
                    </li>
                    @endrole

                    @role(['super-admin', 'admin'])
                    <li>
                        <a href="{{ route('tasks.personal') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('tasks.personal') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-clipboard-check w-5 text-center {{ request()->routeIs('tasks.personal') ? 'text-purple-400' : 'group-hover:text-purple-400' }}"></i>
                            My Tasks
                        </a>
                    </li>
                    @endrole

                    @can('view tasks')
                    <li>
                        <a href="{{ route('tasks.assignments') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('tasks.assignments') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-users-cog w-5 text-center {{ request()->routeIs('tasks.assignments') ? 'text-yellow-400' : 'group-hover:text-yellow-400' }}"></i>
                            Team Assignments
                        </a>
                    </li>
                    @endcan

                    {{-- Sales & CRM --}}
                    @can('view leads')
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sales &amp; CRM</li>

                    <li>
                        <a href="{{ route('leads.pipeline') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('leads.pipeline') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-columns w-5 text-center {{ request()->routeIs('leads.pipeline') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }}"></i>
                            Pipeline
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('leads.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('leads.index') || request()->routeIs('leads.show') || request()->routeIs('leads.create') || request()->routeIs('leads.edit') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-user-plus w-5 text-center group-hover:text-blue-400"></i>
                            All Leads
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('leads.overdue') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('leads.overdue') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-clock w-5 text-center group-hover:text-red-400"></i>
                            Overdue Follow-ups
                        </a>
                    </li>
                    @endcan

                    {{-- Clients & Projects --}}
                    @if(auth()->user()->can('view clients') || auth()->user()->can('view projects') || auth()->user()->can('view invoices'))
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Clients &amp; Projects</li>

                    @can('view clients')
                    <li>
                        <a href="{{ route('clients.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('clients.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-building w-5 text-center group-hover:text-cyan-400"></i>
                            Clients
                        </a>
                    </li>
                    @endcan

                    @can('view projects')
                    <li>
                        <a href="{{ route('projects.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('projects.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-project-diagram w-5 text-center group-hover:text-emerald-400"></i>
                            Projects
                        </a>
                    </li>
                    @endcan

                    @can('view invoices')
                    <li>
                        <a href="{{ route('invoices.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('invoices.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-file-invoice-dollar w-5 text-center group-hover:text-amber-400"></i>
                            Invoices
                        </a>
                    </li>
                    @endcan
                    @endif

                    {{-- Finance --}}
                    @can('view finance')
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Finance</li>

                    <li>
                        <a href="{{ route('finance.dashboard') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('finance.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-chart-pie w-5 text-center group-hover:text-green-400"></i>
                            Monthly P&amp;L
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('finance.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('finance.index') || request()->routeIs('finance.create') || request()->routeIs('finance.show') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-wallet w-5 text-center group-hover:text-green-400"></i>
                            Transactions
                        </a>
                    </li>

                    @role('super-admin')
                    <li>
                        <a href="{{ route('revenue-targets.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('revenue-targets.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-bullseye w-5 text-center group-hover:text-red-400"></i>
                            Revenue Targets
                        </a>
                    </li>
                    @endrole
                    @endcan

                    {{-- Ventures (Super-Admin Only) --}}
                    @role('super-admin')
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ventures</li>
                    @php $ventures = \App\Models\Venture::orderBy('name')->get(); @endphp
                    @foreach($ventures as $vent)
                    <li>
                        <a href="{{ route('ventures.show', $vent->slug) }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->is('ventures/'.$vent->slug) ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas {{ $vent->icon }} w-5 text-center" style="color: {{ $vent->color }}"></i>
                            <span class="truncate">{{ $vent->name }}</span>
                        </a>
                    </li>
                    @endforeach
                    @endrole

                    {{-- Team --}}
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Team</li>

                    <li>
                        <a href="{{ route('daily-reports.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('daily-reports.index') || request()->routeIs('daily-reports.show') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-clipboard-list w-5 text-center group-hover:text-teal-400"></i>
                            Daily Reports
                        </a>
                    </li>

                    @role('super-admin')
                    <li>
                        <a href="{{ route('attendance.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('attendance.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-user-check w-5 text-center group-hover:text-emerald-400"></i>
                            Attendance
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('holidays.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('holidays.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-calendar-alt w-5 text-center group-hover:text-amber-400"></i>
                            Holidays
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('daily-reports.manage') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('daily-reports.manage') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-cog w-5 text-center group-hover:text-slate-400"></i>
                            Report Access
                        </a>
                    </li>
                    @endrole
                    @endif

                    {{-- Administration (Super-Admin Only) --}}
                    @role('super-admin')
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Administration</li>

                    <li>
                        <a href="{{ route('users.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-users w-5 text-center group-hover:text-orange-400"></i>
                            Users
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('roles.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('roles.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-user-shield w-5 text-center group-hover:text-pink-400"></i>
                            Roles
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('permissions.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('permissions.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-key w-5 text-center group-hover:text-yellow-400"></i>
                            Permissions
                        </a>
                    </li>
                    @endrole

                    {{-- Secondary (Grocery — deprioritized) --}}
                    @can('view grocery')
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Other</li>

                    <li>
                        <a href="{{ route('grocery.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('grocery.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-shopping-basket w-5 text-center group-hover:text-red-400"></i>
                            Grocery
                        </a>
                    </li>

                    @can('view finance contacts')
                    <li>
                        <a href="{{ route('finance-contacts.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 rounded-lg transition
                                  {{ request()->routeIs('finance-contacts.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-address-book w-5 text-center group-hover:text-cyan-400"></i>
                            Finance Contacts
                        </a>
                    </li>
                    @endcan
                    @endcan

                </ul>
            </nav>
            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="group w-full flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-slate-800 rounded-lg"><i class="fas fa-sign-out-alt w-5 text-center group-hover:text-red-500"></i> Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden relative">
            <header class="h-16 bg-white/90 backdrop-blur-sm shadow-sm flex items-center justify-between px-6 z-10 border-b border-slate-200 sticky top-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">@yield('header')</h2>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button class="relative p-2 text-slate-400 hover:text-blue-600 transition">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                        </button>
                    </div>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <!-- Profile Link -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 hover:bg-slate-100 p-2 rounded-lg transition">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-slate-700 leading-tight">{{ Auth::user()->name }}</p>
                            @if(!Auth::user()->hasRole('employee'))
                            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">{{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}</p>
                            @endif
                        </div>
                        <div class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold shadow border-2 border-white">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </a>
                </div>
            </header>
            <div class="flex-1 overflow-auto p-6 bg-slate-50">
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
</body>
</html>