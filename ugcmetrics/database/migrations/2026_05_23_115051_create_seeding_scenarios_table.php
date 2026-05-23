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
        Schema::create('seeding_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('target_link');
            $table->string('brand_name');
            $table->text('context'); // Mô tả tình huống thực tế
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_scenarios');
    }
};
