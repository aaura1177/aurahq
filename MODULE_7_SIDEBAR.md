# MODULE 7: Sidebar Restructure & Navigation

## Prerequisites
- ALL Modules 1-6 must be built first. This module reorganizes the sidebar to reflect all new routes.

## What to Build
Completely restructure `resources/views/layouts/admin.blade.php` sidebar navigation. Group items logically, add active-state highlighting, and deprioritize grocery.

## File to Modify
`resources/views/layouts/admin.blade.php` — ONLY the `<nav>` section inside the sidebar `<aside>`.

Do NOT change: header, main content area, logout button, scripts, or any other part of the layout.

---

## New Sidebar Structure

Replace the entire `<nav>` content (the `<ul>` inside the sidebar) with:

```html
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
        <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sales & CRM</li>

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
        <li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Clients & Projects</li>

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
                Monthly P&L
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
                {{ $vent->name }}
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
```

## Key Changes from Current Sidebar:
1. "Dashboard" renamed to "Command Center"
2. Active state highlighting on current page (blue background + white text)
3. "My Work" section at top (My Day, My Tasks, Team Assignments)
4. "Sales & CRM" section (Pipeline, Leads, Overdue)
5. "Clients & Projects" section (Clients, Projects, Invoices)
6. "Finance" section (Monthly P&L, Transactions, Revenue Targets)
7. "Ventures" section (dynamic from DB)
8. "Team" section (Daily Reports, Attendance, Holidays, Report Access)
9. "Administration" section (Users, Roles, Permissions)
10. "Other" section at bottom (Grocery, Finance Contacts — deprioritized)

## Verification
1. Login as super-admin → see full sidebar with all sections
2. Login as employee → see only: Command Center, Team Assignments, Daily Reports, and allowed modules
3. Click each link → correct page loads, active state highlights correctly
4. No broken links or missing routes
5. Sidebar scrolls properly if content overflows
