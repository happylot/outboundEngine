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
        Schema::create('outbound_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('salon_name')->nullable();
            $table->string('platform'); // Facebook, Instagram, etc.
            $table->string('social_id'); // Link or handle
            $table->string('status')->default('new'); // new, researched, drafted, contacted, closed, lost
            $table->integer('score')->default(0);
            $table->json('enrichment_data')->nullable();
            $table->text('generated_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_leads');
    }
};
