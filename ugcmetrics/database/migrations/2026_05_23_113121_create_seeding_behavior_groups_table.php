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
        Schema::create('seeding_behavior_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('daily_limit');
            $table->integer('min_interval_minutes');
            $table->integer('max_interval_minutes');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_behavior_groups');
    }
};
