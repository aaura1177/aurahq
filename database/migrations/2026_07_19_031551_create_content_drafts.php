<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform')->default('linkedin'); // linkedin, x, instagram
            $table->string('content_type')->default('technical'); // technical, win, founder
            $table->string('hook')->nullable();
            $table->longText('body');
            $table->string('hashtags')->nullable();
            $table->string('status')->default('draft'); // draft, approved, scheduled, posted
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->string('post_url')->nullable();       // link to the live post
            $table->json('metrics')->nullable();          // {likes, comments, impressions}
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('content_drafts'); }
};
