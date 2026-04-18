<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name' ,200)->nullable();
            $table->string('code',40)->nullable();
            $table->string('client_name',40)->nullable();
            $table->date('start_date')->nullable();
            $table->date('received_date')->nullable();
            $table->date('client_delivery_date')->nullable();
            $table->date('company_delivery_date')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'on_hold', 'canceled'])->nullable();
            $table->string('priority' ,100)->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->decimal('profit_loss', 10, 2)->nullable();
            $table->integer('team_size')->nullable();
            $table->string('project_category' ,100)->nullable();
            $table->string('location' ,200)->nullable();
            $table->text('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
