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
        Schema::create('seeding_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seeder_account_id')->constrained('seeder_accounts')->onDelete('cascade');
            $table->string('incident_type'); // checkpoint, warning, temporary_block, permanent_ban
            $table->foreignId('trigger_action_id')->nullable()->constrained('seeding_logs')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('recovery_time_minutes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_incidents');
    }
};
