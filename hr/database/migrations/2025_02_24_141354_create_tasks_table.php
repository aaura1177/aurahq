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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('complete_date')->nullable();
            $table->enum('status', ['urgent','pending', 'in_progress', 'completed', 'on_hold', 'canceled'])->default('pending');
            $table->enum('employee_status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->string('priority')->nullable();
            $table->date('estimated_date')->nullable();
            $table->time('actual_hours')->nullable();             
            $table->text('remark' )->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
