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
        Schema::create('grocery_master_items', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('module', ['vegetables', 'blinkit', 'supermart']);
    $table->boolean('is_frequent')->default(false); // If true, auto-add to weekly list
    $table->decimal('default_price', 8, 2)->nullable();
    $table->string('default_qty')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_tables');
    }
};
