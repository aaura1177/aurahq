<ul class="space-y-0.5 px-2">

    {{-- TODAY — home, no section header --}}
    <li>
        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('dashboard') || request()->routeIs('daily-focus.*') || request()->routeIs('tasks.personal') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-sun w-5 text-center {{ request()->routeIs('dashboard') || request()->routeIs('daily-focus.*') || request()->routeIs('tasks.personal') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Today
        </a>
    </li>

    {{-- WORK --}}
    @if(auth()->user()->can('view leads') || auth()->user()->can('view clients') || auth()->user()->can('view projects') || auth()->user()->can('view finance') || auth()->user()->hasRole('super-admin'))
    <li class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Work</li>

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

    @can('view projects')
    <li>
        <a href="{{ route('projects.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('projects.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-project-diagram w-5 text-center {{ request()->routeIs('projects.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Projects
        </a>
    </li>
    @endcan

    @can('view finance')
    <li>
        <a href="{{ route('finance.dashboard') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('finance.*') || request()->routeIs('revenue-targets.*') || request()->routeIs('invoices.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-chart-pie w-5 text-center {{ request()->routeIs('finance.*') || request()->routeIs('revenue-targets.*') || request()->routeIs('invoices.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Finance
        </a>
    </li>
    @endcan

    @role('super-admin')
    <li>
        <a href="{{ route('content-drafts.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('content-drafts.*') || request()->routeIs('content-topics.*') ? 'bg-navy-800 text-white' : 'text-slate-300 hover:bg-navy-800/60 hover:text-white' }}">
            <i class="fas fa-lightbulb w-5 text-center {{ request()->routeIs('content-drafts.*') || request()->routeIs('content-topics.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
            Content
        </a>
    </li>
    @endrole
    @endif

    {{-- MANAGE — quieter secondary zone --}}
    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
    <li class="pt-5 pb-1 px-3 text-[10px] font-bold text-slate-600 uppercase tracking-wider">Manage</li>

    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
    <li>
        <a href="{{ route('daily-reports.index') }}"
           class="group flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs transition
                  {{ request()->routeIs('daily-reports.*') ? 'bg-navy-800 text-white' : 'text-slate-400 hover:bg-navy-800/50 hover:text-slate-200' }}">
            <i class="fas fa-clipboard-list w-5 text-center {{ request()->routeIs('daily-reports.*') ? 'text-brand-400' : 'text-slate-600 group-hover:text-brand-400' }}"></i>
            Daily Reports
        </a>
    </li>
    @endif

    @role('super-admin')
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
            <i class="fas fa-cog w-5 text-center {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'text-brand-400' : 'text-slate-600 group-hover:text-brand-400' }}"></i>
            Admin
        </a>
    </li>
    @endrole
    @endif

</ul>
