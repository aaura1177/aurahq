# Project context: Aurateria HQ (aurahq.online)

This document describes what the application contains, how the pieces fit together, and how day-to-day behavior works. It is intended for developers and AI assistants onboarding to the codebase.

## What this application is

An internal **operations and workforce management** web application branded in the UI as **AURATERIA HQ**. It runs on **Laravel 12** (PHP 8.2+) with a **Blade + Tailwind** admin interface, **Vite** for front-end assets, **Alpine.js** for light interactivity, and a parallel **JSON API** protected by **Laravel Sanctum** for token-based clients.

Core domains:

- **Users, roles, and permissions** (Spatie Laravel Permission).
- **Finance**: contacts and transactions (given vs received).
- **Tasks**: admin “personal” tasks vs employee assignments, task reports (with media), checklists (todos), review workflow.
- **Grocery**: lists, items, daily templates, variable expenses, purchase/pending states.
- **Grocery reports** (aggregated view).
- **Holidays** and **attendance** (super-admin).
- **Daily reports** (morning/evening slots, IST windows, overrides, edit grants, email reminders).

The public site root redirects unauthenticated visitors to **login** (`/` → `route('login')`).

---

## Technology stack

| Layer | Choice |
|--------|--------|
| Framework | Laravel 12 (`laravel/framework`) |
| Auth (web) | Laravel Breeze-style routes in `routes/auth.php` (session guard) |
| API auth | Laravel Sanctum (`HasApiTokens` on `User`) |
| Authorization | Spatie Permission: roles `super-admin`, `admin`, `employee`; granular `view/create/edit/delete …` permissions |
| Front-end build | Vite 7 + `laravel-vite-plugin` |
| CSS | Tailwind (Vite plugin + forms); `resources/css/app.css` |
| JS | `resources/js/app.js`: Axios bootstrap + **Alpine.js** |
| Optional AI | `openai-php/client`; API route `POST /api/ai/command` uses `OPENAI_API_KEY` |

---

## Repository layout (high level)

- `app/Http/Controllers/` — Web controllers (Blade).
- `app/Http/Controllers/Api/` — REST-style API controllers mirroring web features.
- `app/Models/` — Eloquent models (`User`, `Task`, `DailyReport`, grocery/finance models, etc.).
- `app/Mail/` + `resources/views/emails/` — Daily report reminder and disciplinary mail.
- `app/Console/Commands/` — e.g. `SendDailyReportReminders` (`reports:send-reminders`).
- `routes/web.php` — Authenticated Blade application routes.
- `routes/api.php` — Sanctum API; permission middleware on resource groups.
- `routes/auth.php` — Login, registration, password reset (included from `web.php`).
- `resources/views/` — Blade views; `layouts/admin.blade.php` is the main shell (sidebar).
- `database/migrations/` — Schema (users, permissions, tasks, grocery, finance, attendance, holidays, daily reports, overrides, edit grants, Sanctum tokens).
- `database/seeders/DatabaseSeeder.php` — Creates permissions, roles, and optionally a bootstrap super-admin user.
- `config/daily_reports.php` — Feature flags for daily reports (time window bypass, edit grace days).
- `public/` — Web server document root; `public/index.php` front controller; `public/build/` Vite manifest output after `npm run build`.

---

## How a typical HTTP request flows

1. **Web**: Browser hits `public/index.php` → Laravel router loads `routes/web.php`. Guest routes come from `auth.php`. Authenticated routes use `auth` middleware; admin-only sections use `role:super-admin` or `@can` / `permission:` in controllers.
2. **Controller** loads models, authorizes (middleware + inline checks), returns `view(...)` with data.
3. **Layout** `layouts/admin.blade.php` includes `@vite(['resources/css/app.css', 'resources/js/app.js'])`, Chart.js from CDN, Font Awesome, and Alpine (note: layout also references an Alpine CDN script; `app.js` already starts Alpine—worth being aware of possible double initialization in future refactors).
4. **API**: `routes/api.php` — `POST /api/login` issues a token; `auth:sanctum` group exposes CRUD endpoints aligned with permissions. Some routes are restricted to `role:super-admin` (users/roles, holidays, attendance management, daily report admin actions).

---

## Authentication and authorization

### Web (session)

- Standard Laravel session authentication via Breeze-style auth routes.
- After login, users land on **`/dashboard`** (`DashboardController@index`).

### API (Sanctum)

- `POST /api/login` → token-based authentication for mobile or external clients.
- `GET /api/user` returns id, name, email, `is_active`, role names, and all permission names.

### Roles (seeded)

- **`super-admin`**: All permissions; exclusive web routes for `users`, `roles`, `permissions`; sidebar items for holidays, attendance, daily report “Report access” management.
- **`admin`**: Same permission set as super-admin **except** `delete users` (see `DatabaseSeeder`).
- **`employee`**: Narrower defaults—e.g. view/create tasks and task reports, create daily reports (exact set is defined in the seeder and can be extended in the database).

### Permission naming

Permissions follow the pattern `{action} {module}` where modules include: `users`, `roles`, `finance`, `finance contacts`, `tasks`, `grocery`, `grocery templates`, `grocery expenses`, `reports`, `task reports`, `task todos`, `holidays`, `attendance`, `daily reports`, etc. The API applies `permission:…` middleware per route group in `routes/api.php`.

Middleware aliases are registered in `bootstrap/app.php`: `role`, `permission`, `role_or_permission`.

---

## Feature modules (behavioral summary)

### Dashboard (`DashboardController`)

Aggregates for **active** records only where relevant:

- Finance totals: sum of `given`, sum of `received`, net balance.
- Counts: pending tasks, active users, pending grocery line items.
- Chart: last 7 days of finance by day (expense vs income).
- Recent finance transactions (with contact).
- **Daily report compliance (IST)**: after morning deadline (11:00) and evening deadline (17:15), lists employees **marked present** for today who have **not** submitted the corresponding slot—used for dashboard callouts.

### Finance

- **Finance contacts**: CRUD + toggle active (`FinanceContactController`).
- **Finance transactions**: CRUD + toggle; types `given` / `received`, linked to contacts, `transaction_date`, amounts.

### Tasks (`TaskController`, `TaskTodoController`)

- **Categories**: `admin_personal` (creator’s own backlog) vs `employee_assignment` (assigned to an employee).
- **Personal** view (`/tasks/personal`): filters by frequency (`daily`, `top_five`, `urgent`, etc.) and status.
- **Assignments** (`/tasks/assignments`): admins see all assignable tasks and employee filter; employees see only their assignments.
- **Reports**: employees (with permission) attach progress reports, optionally files (storage under `public/storage/task_media/`).
- **Todos**: subtasks on a task; status updates for checklist behavior.
- **Review**: admins can mark tasks reviewed via dedicated route.

### Grocery (`GroceryController`, `GroceryExpenseController`, `ReportController`)

- Shopping lists with items, statuses (pending/purchased), templates for recurring dailies, variable expenses.
- **`/reports`**: grocery-specific reporting view (`ReportController@index`).

### Holidays & attendance (super-admin web; API similar)

- **Holidays**: company holiday calendar CRUD.
- **Attendance**: per-day presence records; **report** view for summaries. Daily report logic uses attendance to determine who **must** file evening reports (present employees only).

### Daily reports (core business rules)

**Model**: `DailyReport` — one row per `user_id` + `date`, with separate **morning** and **evening** payloads:

- Timestamps: `morning_submitted_at`, `evening_submitted_at`.
- Notes and selected task IDs + per-task notes (JSON arrays).

**IST submission windows** (`DailyReport` static helpers):

- Morning: **10:00–11:00** IST (`isWithinMorningWindow`).
- Evening: **16:30–17:15** IST (`isWithinEveningWindow`).
- “Past deadline” flags used by dashboard: after **11:00** / **17:15**.

**Config** (`config/daily_reports.php`, env-driven):

- `DAILY_REPORT_IGNORE_TIME` → `ignore_time_window`: if true, employees are not bound to clock windows (testing).
- `DAILY_REPORT_EDIT_DAYS` → `employee_edit_days`: how many days after the report date an employee may still edit (default 1).

**Overrides and grants**

- **`ReportSubmissionOverride`**: super-admin can allow submission outside the normal window for a given user/date/slot (`DailyReportController` manage routes + API `DailyReportManageApiController`).
- **`ReportEditGrant`**: allows editing outside the normal window rules when valid.

**Evening rule**: Non–super-admin users must be in the **present** list from `AttendanceRecord::getPresentEmployeeIdsForDate` for that date to submit the evening slot.

**Permissions**

- `create daily reports` middleware on create/store (web).
- Index/show: controller narrows employees vs own reports for `employee` role.

### Scheduled email reminders

Defined in `bootstrap/app.php` `withSchedule`:

| Time (Asia/Kolkata) | Command | Purpose |
|---------------------|---------|---------|
| 10:20 | `reports:send-reminders --slot=morning --type=reminder` | Nudge employees missing morning report |
| 17:00 | `--slot=evening --type=reminder` | Evening reminder |
| 11:00 | `--slot=morning --type=disciplinary` | Escalation email + admin summary |
| 17:15 | `--slot=evening --type=disciplinary` | Same for evening |

Implementation: `App\Console\Commands\SendDailyReportReminders` compares **present** employee IDs to those who submitted the slot; mails individuals and sends a summary to all `super-admin` emails.

**Operations note:** Laravel’s scheduler requires a system cron entry running `php artisan schedule:run` every minute on the server, and a working **queue/mail** configuration if mail is sent asynchronously (depends on `MAIL_*` and queue driver).

### AI command API (`Api\AiController`)

Authenticated `POST /api/ai/command` sends the user’s natural-language `command` to OpenAI (`gpt-3.5-turbo`) with function-calling style tools to **create tasks** or **add grocery items**. Requires `OPENAI_API_KEY` in `.env`. Intended as an optional assistant, not a core path for CRUD.

---

## Front-end assets

- **Vite** inputs: `resources/css/app.css`, `resources/js/app.js` (`vite.config.js`).
- **npm scripts**: `npm run dev` (Vite dev server), `npm run build` (production assets to `public/build/`).
- **Composer “dev” script** (from `composer.json`): runs `php artisan serve`, queue listener, `pail` logs, and `npm run dev` together via `concurrently`.

---

## Data store

- Default `.env.example` uses **`DB_CONNECTION=sqlite`** with `database/database.sqlite`. Production commonly uses MySQL; adjust `config/database.php` via env.
- **Session** driver in example: `database` (session table from default Laravel migrations).
- **Queue** in example: `database` (jobs table).

---

## Environment variables (non-exhaustive)

Set in `.env` (never commit real secrets). Examples:

- `APP_*`, `DB_*`, `MAIL_*`
- `OPENAI_API_KEY` — AI command endpoint
- `DAILY_REPORT_IGNORE_TIME`, `DAILY_REPORT_EDIT_DAYS` — daily report behavior

See `.env.example` for the baseline Laravel keys.

---

## Testing and quality

- PHPUnit config: `phpunit.xml`; tests under `tests/Feature` and `tests/Unit` (Breeze-style auth/profile tests included).
- `composer test` runs `php artisan test`.

---

## Security and deployment notes

- **Sanctum**: protect API tokens; use HTTPS in production; configure `SANCTUM_STATEFUL_DOMAINS` if SPA on another origin.
- **Roles**: `super-admin` bypasses many UI gates; protect admin routes and server SSH separately.
- **Seeder**: `DatabaseSeeder` can create an initial super-admin if the email does not exist. **Change any bootstrap password immediately** after first deploy; prefer env-driven admin provisioning in production.
- **Uploaded files**: Task and report media live under `public/storage/`; ensure `php artisan storage:link` and disk permissions are correct.
- **Scheduler**: without cron + `schedule:run`, reminder emails will not fire.

---

## Quick reference: main web routes (authenticated)

| Area | Prefix / names | Notes |
|------|----------------|--------|
| Dashboard | `GET /dashboard` | Metrics + compliance |
| Profile | `GET/PATCH /profile`, password update | Breeze-style |
| Admin only (`super-admin`) | `users`, `roles`, `permissions` | Resource routes + user toggle |
| Finance | `finance-contacts`, `finance` | Toggle routes |
| Tasks | `tasks`, `tasks/personal`, `tasks/assignments`, nested report/todo routes | Permission middleware on controller |
| Grocery | `grocery`, templates, purchase/pending, `grocery-expenses` | |
| Reports | `GET /reports` | Grocery reports |
| Holidays / attendance | `holidays`, `attendance`, `attendance/report` | Super-admin in sidebar |
| Daily reports | `daily-reports`, `daily-reports/manage` | Manage = overrides/grants |

Full definitions: `routes/web.php` and `routes/api.php`.

---

## Glossary

- **IST**: Asia/Kolkata timezone; all daily report windows and the scheduler use this zone.
- **Slot**: `morning` or `evening` daily report segment.
- **Present list**: Employees marked present in attendance for a date; drives evening report eligibility and reminder recipient lists.

---

*Last updated from codebase scan: 2026-04-18. If behavior drifts from this document, treat migrations, routes, and `bootstrap/app.php` scheduling as the source of truth.*
