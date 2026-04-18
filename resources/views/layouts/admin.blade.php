<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="//[unpkg.com/alpinejs](https://unpkg.com/alpinejs)" defer></script>
    <style>
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
                    <li><a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-home w-5 text-center group-hover:text-blue-400"></i> Dashboard</a></li>
                    
                    <!-- Administration -->
                    @if(auth()->user()->can('view users') || auth()->user()->hasRole('super-admin'))
                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Administration</li>
                    
                    @can('view users')
                    <li><a href="{{ route('users.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-users w-5 text-center group-hover:text-orange-400"></i> Users</a></li>
                    @endcan
                    
                    @can('view roles')
                    <li><a href="{{ route('roles.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-user-shield w-5 text-center group-hover:text-pink-400"></i> Roles</a></li>
                    <li><a href="{{ route('permissions.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-key w-5 text-center group-hover:text-yellow-400"></i> Permissions</a></li>
                    @endcan
                    @endif

                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Task Management</li>
                    @role(['super-admin', 'admin'])
                    <li><a href="{{ route('tasks.personal') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-clipboard-check w-5 text-center group-hover:text-purple-400"></i> My Personal Tasks</a></li>
                    @endrole
                    
                    @can('view tasks')
                    <li><a href="{{ route('tasks.assignments') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-users-cog w-5 text-center group-hover:text-yellow-400"></i> Assignments</a></li>
                    @endcan

                    <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Modules</li>
                    @can('view finance contacts')
                    <li><a href="{{ route('finance-contacts.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-address-book w-5 text-center group-hover:text-cyan-400"></i> Finance Contacts</a></li>
                    @endcan
                    
                    @can('view finance')
                    <li><a href="{{ route('finance.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-wallet w-5 text-center group-hover:text-green-400"></i> All Transactions</a></li>
                    @endcan
                    
                    @can('view grocery')
                    <li><a href="{{ route('grocery.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-shopping-basket w-5 text-center group-hover:text-red-400"></i> Grocery</a></li>
                    @endcan
                    
                    @role('super-admin')
                    <li><a href="{{ route('holidays.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-calendar-alt w-5 text-center group-hover:text-amber-400"></i> Holidays</a></li>
                    <li><a href="{{ route('attendance.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-user-check w-5 text-center group-hover:text-emerald-400"></i> Attendance</a></li>
                    @endrole
                    
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
                    <li><a href="{{ route('daily-reports.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-clipboard-list w-5 text-center group-hover:text-teal-400"></i> Daily Reports</a></li>
                    @endif
                    @role('super-admin')
                    <li><a href="{{ route('daily-reports.manage') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-cog w-5 text-center group-hover:text-teal-400"></i> Report access</a></li>
                    @endrole
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