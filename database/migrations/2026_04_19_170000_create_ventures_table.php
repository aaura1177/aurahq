<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            $table->string('partner_name')->nullable();
            $table->boolean('partner_funded')->default(false);
            $table->string('color', 7)->default('#6C63FF');
            $table->string('icon', 50)->default('fa-rocket');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventures');
    }
};
