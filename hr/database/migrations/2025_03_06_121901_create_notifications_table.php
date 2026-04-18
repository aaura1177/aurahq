<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();;
            $table->unsignedBigInteger('user_id')->nullable();;
            $table->string('title',200)->nullable();;
            $table->text('message')->nullable();;
            $table->tinyInteger('status')->default(0);
            $table->enum('is_read', ['0', '1'])->default('0');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
