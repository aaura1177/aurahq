<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_targets', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->decimal('target_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_targets');
    }
};
