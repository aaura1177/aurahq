<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            //
            $table->decimal('per_minute_rate', 8, 2)->default(0)->after('project_id');
            $table->decimal('pending_amount', 10, 2)->default(0)->after('per_minute_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            //
         $table->dropColumn(['per_minute_rate', 'pending_amount']);
        });
    }
};
