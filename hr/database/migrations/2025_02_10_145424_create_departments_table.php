<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable(); 
            $table->string('code')->nullable(); 
            $table->text('description')->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->integer('total_employee')->default(0); 
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('departments');
    }
};
