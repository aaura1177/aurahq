<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('type');
            $table->string('venture', 50)->default('aurateria')->after('category');
            $table->boolean('is_recurring')->default(false)->after('venture');
            $table->integer('recurring_day')->nullable()->after('is_recurring');
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['category', 'venture', 'is_recurring', 'recurring_day']);
        });
    }
};
