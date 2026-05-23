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
        Schema::create('seeder_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('platform'); // facebook, instagram, threads, reddit, tiktok
            $table->string('profile_url')->nullable();
            $table->string('age_group'); // < 6 months, 6-12 months, 1-3 years, > 3 years
            $table->foreignId('device_id')->nullable()->constrained('seeding_devices')->nullOnDelete();
            $table->foreignId('behavior_group_id')->nullable()->constrained('seeding_behavior_groups')->nullOnDelete();
            $table->string('status')->default('active'); // active, warning, checkpoint, cooldown, dead
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('cooldown_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeder_accounts');
    }
};
