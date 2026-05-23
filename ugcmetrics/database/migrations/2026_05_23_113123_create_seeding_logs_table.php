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
        Schema::create('seeding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seeder_account_id')->constrained('seeder_accounts')->onDelete('cascade');
            $table->string('action_type'); // comment, like, post, share, follow
            $table->string('target_url')->nullable();
            $table->string('content_type')->nullable(); // copy-paste, hand-written-short, hand-written-long
            $table->text('content')->nullable();
            $table->string('status')->default('success'); // success, warning, checkpoint_triggered
            $table->json('response_metadata')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_logs');
    }
};
