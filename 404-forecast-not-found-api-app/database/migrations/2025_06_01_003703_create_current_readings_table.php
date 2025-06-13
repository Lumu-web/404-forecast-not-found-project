<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('current_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('weather_provider_id')->constrained('weather_providers')->onDelete('cascade');
            $table->timestamp('timestamp');
            $table->float('temperature');
            $table->float('humidity');
            $table->float('wind_speed');
            $table->string('conditions');
            $table->timestamps();

            $table->unique(['location_id', 'weather_provider_id', 'timestamp'], 'current_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_readings');
    }
};
