<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_focuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->date('date');

            $table->string('task_1_title')->nullable();
            $table->boolean('task_1_completed')->default(false);
            $table->foreignId('task_1_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->string('task_2_title')->nullable();
            $table->boolean('task_2_completed')->default(false);
            $table->foreignId('task_2_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->string('task_3_title')->nullable();
            $table->boolean('task_3_completed')->default(false);
            $table->foreignId('task_3_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->string('energy_level', 20)->nullable();
            $table->text('end_of_day_note')->nullable();
            $table->text('wins')->nullable();
            $table->text('tomorrow_focus')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_focuses');
    }
};
