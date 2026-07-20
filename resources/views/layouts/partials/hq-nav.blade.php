<ul class="space-y-0.5 px-2">

    {{-- TODAY — home, no section header --}}
    <li>
        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('dashboard') || request()->routeIs('daily-focus.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-house w-5 text-center {{ request()->routeIs('dashboard') || request()->routeIs('daily-focus.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Today
        </a>
    </li>

    {{-- MONEY --}}
    @if(auth()->user()->can('view finance') || auth()->user()->can('view invoices') || auth()->user()->can('view revenue targets'))
    <li class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Money</li>

    @can('view finance')
    <li>
        <a href="{{ route('finance.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('finance.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-wallet w-5 text-center {{ request()->routeIs('finance.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Finance
        </a>
    </li>
    @endcan

    @can('view invoices')
    <li>
        <a href="{{ route('invoices.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('invoices.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center {{ request()->routeIs('invoices.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Invoices
        </a>
    </li>
    @endcan

    @can('view revenue targets')
    <li>
        <a href="{{ route('revenue-targets.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('revenue-targets.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-bullseye w-5 text-center {{ request()->routeIs('revenue-targets.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Targets
        </a>
    </li>
    @endcan
    @endif

    {{-- SALES --}}
    @if(auth()->user()->can('view leads') || auth()->user()->can('view clients'))
    <li class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Sales</li>

    @can('view leads')
    <li>
        <a href="{{ route('leads.pipeline') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('leads.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-columns w-5 text-center {{ request()->routeIs('leads.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Pipeline
        </a>
    </li>
    @endcan

    @can('view clients')
    <li>
        <a href="{{ route('clients.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('clients.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-building w-5 text-center {{ request()->routeIs('clients.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Clients
        </a>
    </li>
    @endcan
    @endif

    {{-- DELIVERY --}}
    @if(auth()->user()->can('view projects') || auth()->user()->can('view tasks') || auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
    <li class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Delivery</li>

    @can('view projects')
    <li>
        <a href="{{ route('projects.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('projects.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-diagram-project w-5 text-center {{ request()->routeIs('projects.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Projects
        </a>
    </li>
    @endcan

    @can('view tasks')
    <li>
        <a href="{{ route('tasks.personal') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('tasks.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-list-check w-5 text-center {{ request()->routeIs('tasks.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Tasks
        </a>
    </li>
    @endcan

    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
    <li>
        <a href="{{ route('daily-reports.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('daily-reports.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-clipboard-list w-5 text-center {{ request()->routeIs('daily-reports.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Daily Reports
        </a>
    </li>
    @endif
    @endif

    {{-- GROWTH --}}
    @role('super-admin')
    <li class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Growth</li>

    <li>
        <a href="{{ route('content-drafts.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('content-drafts.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-feather w-5 text-center {{ request()->routeIs('content-drafts.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Content
        </a>
    </li>

    <li>
        <a href="{{ route('content-topics.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('content-topics.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-book w-5 text-center {{ request()->routeIs('content-topics.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Knowledge Bank
        </a>
    </li>
    @endrole

    {{-- MANAGE — quieter secondary zone --}}
    @role('super-admin')
    <li class="pt-5 pb-1 px-3 text-[10px] font-bold text-slate-600 uppercase tracking-wider">Manage</li>

    <li>
        <a href="{{ route('ventures.index') }}"
           class="group flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs transition
                  {{ request()->routeIs('ventures.*') ? 'bg-navy-800 text-white' : 'text-slate-400 hover:bg-navy-800/50 hover:text-slate-200' }}">
            <i class="fas fa-rocket w-5 text-center {{ request()->routeIs('ventures.*') ? 'text-brand-400' : 'text-slate-600 group-hover:text-brand-400' }}"></i>
            Ventures
        </a>
    </li>

    <li>
        <a href="{{ route('users.index') }}"
           class="group flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs transition
                  {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'bg-navy-800 text-white' : 'text-slate-400 hover:bg-navy-800/50 hover:text-slate-200' }}">
            <i class="fas fa-users-gear w-5 text-center {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'text-brand-400' : 'text-slate-600 group-hover:text-brand-400' }}"></i>
            Users
        </a>
    </li>
    @endrole

</ul>
