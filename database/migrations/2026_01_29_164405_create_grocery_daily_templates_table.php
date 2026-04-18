<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('grocery_daily_templates', function (Blueprint $table) {
        $table->id();
        $table->string('type')->default('today'); // Category for the template
        $table->string('item_name');
        $table->string('qty');
        $table->decimal('estimated_price', 8, 2)->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_daily_templates');
    }
};
