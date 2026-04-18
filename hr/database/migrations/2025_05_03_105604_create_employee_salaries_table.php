<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalariesTable extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('hra', 10, 2)->nullable();
            $table->decimal('da', 10, 2)->nullable();
            $table->decimal('ta', 10, 2)->nullable();
            $table->decimal('other_allowance', 10, 2)->nullable();
            $table->decimal('deductions', 10, 2)->nullable();
            $table->decimal('net_salary', 10, 2)->nullable();
            $table->decimal('workform_salary', 10, 2)->nullable();
            $table->decimal('attendance_salay', 10, 2)->nullable();
            $table->decimal('weekoffsalary', 10, 2)->nullable();
            $table->decimal('holiday_salary', 10, 2)->nullable();
            $table->time('company_working_hours')->nullable();
            $table->time('home_working_hours')->nullable();
            $table->date('salary_month');
            $table->integer('paid')->nullable();
            $table->integer('unpaid')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
}
