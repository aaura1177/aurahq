<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venture_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('type', 50)->default('update');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venture_updates');
    }
};
