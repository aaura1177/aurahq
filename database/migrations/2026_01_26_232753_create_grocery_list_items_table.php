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
    Schema::create('grocery_list_items', function (Blueprint $table) {
        $table->id();
        $table->string('item_name');
        $table->string('qty');
        $table->string('type')->default('vegetables'); // vegetables, blinkit, supermart, others, today
        $table->decimal('estimated_price', 8, 2)->default(0);
        $table->decimal('actual_cost', 8, 2)->nullable(); // Track actual spend
        $table->date('date')->nullable(); // For 'today' items
        $table->string('status')->default('pending');
        $table->text('remark')->nullable();
        $table->boolean('is_frequent')->default(false);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_list_items');
    }
};
