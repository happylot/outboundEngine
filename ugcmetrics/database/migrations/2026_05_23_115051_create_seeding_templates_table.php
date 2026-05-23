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
        Schema::create('seeding_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('seeding_scenarios')->onDelete('cascade');
            $table->string('angle'); // tham, san, si, brand_recall
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_templates');
    }
};
