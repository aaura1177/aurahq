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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('category'); 
        $table->string('priority')->default('normal');
        $table->string('frequency')->default('once'); // daily, weekly, top_five
        $table->foreignId('assigned_to')->nullable()->constrained('users');
        $table->foreignId('created_by')->constrained('users');
        $table->string('status')->default('pending'); 
        $table->date('due_date')->nullable();
        
        $table->string('media_path')->nullable();

        $table->text('employee_remark')->nullable(); 
        $table->text('admin_remark')->nullable(); 
        $table->string('admin_media_path')->nullable();
        $table->text('admin_private_note')->nullable();
        $table->integer('rating')->nullable(); 
        
        $table->boolean('is_active')->default(true);
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
