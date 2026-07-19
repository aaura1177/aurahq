<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add a clean recurrence column (replaces the overloaded 'frequency')
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'recurrence')) {
                $table->string('recurrence')->default('none')->after('priority'); // none, daily, weekly
            }
            if (!Schema::hasColumn('tasks', 'is_starred')) {
                $table->boolean('is_starred')->default(false)->after('recurrence'); // replaces 'top_five'
            }
            if (!Schema::hasColumn('tasks', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_starred');
            }
        });

        // 2. Migrate existing data: frequency -> recurrence + is_starred
        DB::table('tasks')->where('frequency', 'top_five')->update(['is_starred' => true, 'recurrence' => 'none']);
        DB::table('tasks')->where('frequency', 'daily')->update(['recurrence' => 'daily']);
        DB::table('tasks')->where('frequency', 'weekly')->update(['recurrence' => 'weekly']);

        // 3. Fold is_active into status: inactive tasks become 'archived'
        DB::table('tasks')->where('is_active', false)->update(['status' => 'archived']);

        // Note: we KEEP is_active and frequency columns for now (don't drop yet — safety).
        // A later migration can drop them once the app no longer references them.
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence', 'is_starred', 'sort_order']);
        });
    }
};
