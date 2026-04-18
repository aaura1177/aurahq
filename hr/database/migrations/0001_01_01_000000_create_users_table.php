<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
             $table->id();
            $table->string('username',100)->unique()->nullable(); 
            $table->string('email',100)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('password')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->boolean('status')->default(true);
            $table->enum('account_type', ['admin', 'user'])->default('user');
            $table->boolean('email_notification')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->integer('login_attempts')->default(0);
            $table->boolean('account_locked')->default(false);
            $table->string('password_reset_token')->nullable();
            $table->timestamp('token_expire')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('image')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });


        DB::table('users')->insert([
            'username' => 'Aurateria',
            'email' => 'aura@gmail.com',
            'password' => Hash::make('123456'), 
            'mobile' => '9509354739',
            'account_type' => 'admin',
            'is_verified' => true,
       
        ]);
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
