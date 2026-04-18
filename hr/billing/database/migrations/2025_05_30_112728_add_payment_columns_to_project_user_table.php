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
          $table->decimal('hourly_rate', 8, 2)->default(0)->after('status');
        $table->decimal('paid_hours', 8, 2)->default(0)->after('hourly_rate');
        $table->boolean('is_fully_paid')->default(false)->after('paid_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            //
             $table->dropColumn(['hourly_rate', 'paid_hours', 'is_fully_paid']);
    
        });
    }
};
