<ul class="space-y-0.5 px-2">

    {{-- Command Center --}}
    <li>
        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-th-large w-5 text-center {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'group-hover:text-blue-400' }}"></i>
            Command Center
        </a>
    </li>

    {{-- My Work (Super-Admin Only) --}}
    @role('super-admin')
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">My Work</li>

    <li>
        <a href="{{ route('daily-focus.today') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('daily-focus.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-sun w-5 text-center {{ request()->routeIs('daily-focus.*') ? 'text-amber-400' : 'group-hover:text-amber-400' }}"></i>
            My Day
        </a>
    </li>
    @endrole

    @role(['super-admin', 'admin'])
    <li>
        <a href="{{ route('tasks.personal') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('tasks.personal') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-clipboard-check w-5 text-center {{ request()->routeIs('tasks.personal') ? 'text-purple-400' : 'group-hover:text-purple-400' }}"></i>
            My Tasks
        </a>
    </li>
    @endrole

    @role('super-admin')
    <li>
        <a href="{{ route('content-topics.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('content-topics.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-lightbulb w-5 text-center {{ request()->routeIs('content-topics.*') ? 'text-yellow-400' : 'group-hover:text-yellow-400' }}"></i>
            Content Topics
        </a>
    </li>
    @endrole

    @can('view tasks')
    <li>
        <a href="{{ route('tasks.assignments') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('tasks.assignments') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-users-cog w-5 text-center {{ request()->routeIs('tasks.assignments') ? 'text-yellow-400' : 'group-hover:text-yellow-400' }}"></i>
            Team Assignments
        </a>
    </li>
    @endcan

    {{-- Sales & CRM --}}
    @can('view leads')
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Sales &amp; CRM</li>

    <li>
        <a href="{{ route('leads.pipeline') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('leads.pipeline') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-columns w-5 text-center {{ request()->routeIs('leads.pipeline') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }}"></i>
            Pipeline
        </a>
    </li>

    <li>
        <a href="{{ route('leads.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('leads.index') || request()->routeIs('leads.show') || request()->routeIs('leads.create') || request()->routeIs('leads.edit') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-user-plus w-5 text-center group-hover:text-blue-400"></i>
            All Leads
        </a>
    </li>

    <li>
        <a href="{{ route('leads.overdue') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('leads.overdue') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-clock w-5 text-center group-hover:text-red-400"></i>
            Overdue Follow-ups
        </a>
    </li>
    @endcan

    {{-- Clients & Projects --}}
    @if(auth()->user()->can('view clients') || auth()->user()->can('view projects') || auth()->user()->can('view invoices'))
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Clients &amp; Projects</li>

    @can('view clients')
    <li>
        <a href="{{ route('clients.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('clients.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-building w-5 text-center group-hover:text-cyan-400"></i>
            Clients
        </a>
    </li>
    @endcan

    @can('view projects')
    <li>
        <a href="{{ route('projects.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('projects.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-project-diagram w-5 text-center group-hover:text-emerald-400"></i>
            Projects
        </a>
    </li>
    @endcan

    @can('view invoices')
    <li>
        <a href="{{ route('invoices.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('invoices.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center group-hover:text-amber-400"></i>
            Invoices
        </a>
    </li>
    @endcan
    @endif

    {{-- Finance --}}
    @can('view finance')
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Finance</li>

    <li>
        <a href="{{ route('finance.dashboard') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('finance.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-5 text-center group-hover:text-green-400"></i>
            Monthly P&amp;L
        </a>
    </li>

    <li>
        <a href="{{ route('finance.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('finance.index') || request()->routeIs('finance.create') || request()->routeIs('finance.show') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-wallet w-5 text-center group-hover:text-green-400"></i>
            Transactions
        </a>
    </li>

    @role('super-admin')
    <li>
        <a href="{{ route('revenue-targets.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('revenue-targets.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-bullseye w-5 text-center group-hover:text-red-400"></i>
            Revenue Targets
        </a>
    </li>
    @endrole
    @endcan

    {{-- Ventures (Super-Admin Only) --}}
    @role('super-admin')
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Ventures</li>
    <li>
        <a href="{{ route('ventures.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('ventures.index') || request()->routeIs('ventures.create') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-th-list w-5 text-center group-hover:text-blue-400"></i>
            All Ventures
        </a>
    </li>
    @foreach($sidebarVentures as $vent)
    <li>
        <a href="{{ route('ventures.show', $vent->slug) }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->is('ventures/'.$vent->slug) ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas {{ $vent->icon }} w-5 text-center" style="color: {{ $vent->color }}"></i>
            <span class="truncate">{{ $vent->name }}</span>
        </a>
    </li>
    @endforeach
    @endrole

    {{-- Team --}}
    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('create daily reports'))
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Team</li>

    <li>
        <a href="{{ route('daily-reports.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('daily-reports.index') || request()->routeIs('daily-reports.show') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-clipboard-list w-5 text-center group-hover:text-teal-400"></i>
            Daily Reports
        </a>
    </li>

    @role('super-admin')
    <li>
        <a href="{{ route('attendance.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('attendance.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-user-check w-5 text-center group-hover:text-emerald-400"></i>
            Attendance
        </a>
    </li>

    <li>
        <a href="{{ route('holidays.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('holidays.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-calendar-alt w-5 text-center group-hover:text-amber-400"></i>
            Holidays
        </a>
    </li>

    <li>
        <a href="{{ route('daily-reports.manage') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('daily-reports.manage') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-cog w-5 text-center group-hover:text-slate-400"></i>
            Report Access
        </a>
    </li>
    @endrole
    @endif

    {{-- Administration (Super-Admin Only) --}}
    @role('super-admin')
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Administration</li>

    <li>
        <a href="{{ route('users.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-users w-5 text-center group-hover:text-orange-400"></i>
            Users
        </a>
    </li>

    <li>
        <a href="{{ route('roles.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('roles.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-user-shield w-5 text-center group-hover:text-pink-400"></i>
            Roles
        </a>
    </li>

    <li>
        <a href="{{ route('permissions.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('permissions.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-key w-5 text-center group-hover:text-yellow-400"></i>
            Permissions
        </a>
    </li>
    @endrole

    {{-- Secondary (Grocery — deprioritized) --}}
    @can('view grocery')
    <li class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Other</li>

    <li>
        <a href="{{ route('grocery.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('grocery.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-shopping-basket w-5 text-center group-hover:text-red-400"></i>
            Grocery
        </a>
    </li>

    @can('view finance contacts')
    <li>
        <a href="{{ route('finance-contacts.index') }}"
           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('finance-contacts.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-address-book w-5 text-center group-hover:text-cyan-400"></i>
            Finance Contacts
        </a>
    </li>
    @endcan
    @endcan

</ul>
