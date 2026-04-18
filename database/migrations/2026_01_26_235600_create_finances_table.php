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
    Schema::create('finances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('finance_contact_id')->constrained()->onDelete('cascade');
        $table->foreignId('created_by')->constrained('users');
        $table->decimal('amount', 10, 2);
        $table->string('type'); // given/received
        $table->string('method'); // cash/upi/bank
        $table->text('remark')->nullable();
        $table->string('proof_path')->nullable();
        $table->timestamp('transaction_date');
        $table->boolean('is_active')->default(true); // Added
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
