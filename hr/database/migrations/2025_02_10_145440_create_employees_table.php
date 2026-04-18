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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->string('email' ,100)->unique()->nullable();
            $table->text('password')->nullable();
            $table->string('emp_id',80)->unique();
            $table->string('image')->nullable();
            $table->string('name',80)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->date('date_of_joining')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('position')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->enum('employee_type', ['permanent', 'contract'])->default('permanent');
            $table->integer('login_attempts')->default(0);
            $table->boolean('account_locked')->default(false);
            $table->decimal('annual_bonus', 10, 2)->nullable();
            $table->boolean('is_on_leave')->default(false);
            $table->integer('notice_period')->default(0); 
            $table->string('work_time')->nullable();
            $table->boolean('work_from_home')->default(false);
            $table->text('address')->nullable();
            $table->string('emergency_contact_number', 15)->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->string('resume')->nullable(); 
            $table->string('working_hours')->nullable();
            $table->string('biometric_id')->nullable();
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
