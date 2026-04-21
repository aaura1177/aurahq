# AuraHQ v2 — Cursor/Claude Code Build Prompts

## How to Use These Files

### Setup
1. Copy ALL `.md` files from this folder into your AuraHQ project root (alongside `PROJECT_CONTEXT.md`)
2. Open your project in Cursor or Claude Code

### Build Order (DO NOT SKIP OR REORDER)
```
Module 1 → Module 2 → Module 3 → Module 4 → Module 5 → Module 6 → Module 7 → Module 8
```

### For Each Module

**In Cursor:**
1. Open the module file (e.g., `MODULE_1_CEO_DASHBOARD.md`)
2. Open Cursor Chat (Cmd+L)
3. Type: `Read MODULE_1_CEO_DASHBOARD.md and build everything it describes. Follow existing codebase patterns.`
4. Let Cursor generate the code
5. Review and accept changes
6. Run verification commands (listed at bottom of each module file)
7. Test manually in browser
8. ONLY THEN move to next module

**In Claude Code:**
1. Run: `cat MODULE_1_CEO_DASHBOARD.md` to give Claude Code context
2. Tell it: `Build Module 1 as described in this file.`
3. Same verification + testing process

### After Each Module — ALWAYS RUN:
```bash
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
php artisan permission:cache-reset    # if permissions changed
php artisan route:list | head -50     # verify routes
php artisan serve                     # test in browser
```

### If Something Breaks:
- Check `php artisan migrate:status` — are all migrations run?
- Check `php artisan route:list` — are routes registered?
- Check browser console for JS errors
- Check Laravel logs: `tail -f storage/logs/laravel.log`

## File List
| File | Module | Description | Est. Time |
|------|--------|-------------|-----------|
| MODULE_1_CEO_DASHBOARD.md | Dashboard | New CEO command center | 2-4 hours |
| MODULE_2_CRM_LEADS.md | CRM | Lead pipeline + activities | 4-6 hours |
| MODULE_3_CLIENTS_PROJECTS.md | Clients | Clients + projects + invoices | 4-6 hours |
| MODULE_4_FINANCIAL_INTELLIGENCE.md | Finance | Categories + P&L + targets | 3-4 hours |
| MODULE_5_VENTURES.md | Ventures | GicoGifts, AIGather, Medical AI | 2-3 hours |
| MODULE_6_MY_DAY.md | Productivity | 3-task daily focus for CEO | 2-3 hours |
| MODULE_7_SIDEBAR.md | Navigation | Full sidebar restructure | 1-2 hours |
| MODULE_8_API_COMPLETENESS.md | API | Verify all API endpoints | 2-4 hours |
| **TOTAL** | | | **20-32 hours** |

## Important Notes
- Each module file is SELF-CONTAINED — it has everything Cursor needs
- Do NOT feed multiple module files at once — one at a time
- The blueprint (aurahq_v2_blueprint.md) is the master reference if you need more context
- PROJECT_CONTEXT.md describes the existing codebase patterns
