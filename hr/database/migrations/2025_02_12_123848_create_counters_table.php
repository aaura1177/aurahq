<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('counter_name', 100)->unique()->nullable();
            $table->string('prefix', 100)->nullable();
            $table->bigInteger('count')->default(1000);
            $table->boolean('status')->nullable();
            $table->timestamps();
        });

        DB::table('counters')->insert([
            [
                'counter_name' => 'emp_id',
                'prefix' => 'EMP',
                'count' => 1001, 
            ],
            [
                'counter_name' => 'it',
                'prefix' => 'SOFT',
                'count' => 1001,
            ]
        ]);
    }

  
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
