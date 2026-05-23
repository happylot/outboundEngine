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
        Schema::create('seeding_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('device_model');
            $table->string('os_version');
            $table->string('connection_type'); // Wi-Fi, 4G, Proxy
            $table->string('proxy_info')->nullable();
            $table->string('status')->default('active'); // active, inactive, maintenance
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeding_devices');
    }
};
