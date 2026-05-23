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
        Schema::create('outbound_kb_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'sales_deck', 'objection_handling', 'product_info', 'target_persona'
            $table->string('title');
            $table->text('content');
            $table->text('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_kb_entries');
    }
};
