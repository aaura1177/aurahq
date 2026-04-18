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
        Schema::table('project_user_payments', function (Blueprint $table) {
            //
           $table->decimal('pending_amount', 10, 2)->nullable()->after('amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_user_payments', function (Blueprint $table) {
            //
           $table->dropColumn('pending_amount');
        });
    }
};
