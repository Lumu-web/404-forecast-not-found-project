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
        Schema::create('forecast_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('weather_provider_id')->constrained('weather_providers')->onDelete('cascade');
            $table->date('date');
            $table->float('high');
            $table->float('low');
            $table->float('precipitation_prob');
            $table->timestamps();

            $table->unique(['location_id', 'weather_provider_id', 'date'], 'forecast_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecast_readings');
    }
};
