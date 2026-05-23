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
        Schema::create('seeding_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seeder_account_id')->nullable()->constrained('seeder_accounts')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_uid')->nullable();
            $table->string('platform'); // facebook, instagram, threads, reddit, tiktok, etc.
            $table->string('profile_url')->nullable();
            $table->string('comment_url')->nullable();
            $table->text('content')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('new'); // new, syncing, synced, failed
            $table->string('zenify_lead_id')->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_leads');
    }
};
