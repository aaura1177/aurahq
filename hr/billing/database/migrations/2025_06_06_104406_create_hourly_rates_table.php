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
        Schema::create('hourly_rates', function (Blueprint $table) {
            $table->id();
                  // Foreign keys
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');

            // Rate columns
            $table->decimal('h_rate', 10, 2); // Hourly Rate
            $table->decimal('m_rate', 10, 4); // Minute Rate

            // Date of rate assignment
            $table->date('date');

            $table->timestamps();

            // Foreign key constraints (optional)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hourly_rates');
    }
};
