<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('angle')->nullable();           // the specific point/hook
            $table->string('content_type')->default('technical'); // technical, win, founder
            $table->string('status')->default('available');       // available, used
            $table->timestamp('used_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('content_topics');
    }
};
