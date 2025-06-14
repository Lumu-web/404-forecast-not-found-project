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
        Schema::create('weather_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->timestamp('forecast_at');
            $table->float('temperature');
            $table->float('feels_like');
            $table->integer('pressure');
            $table->integer('humidity');
            $table->float('wind_speed');
            $table->integer('wind_deg');
            $table->string('weather_main');
            $table->string('weather_description');
            $table->string('weather_icon');
            $table->uuid('source_batch_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_forecasts');
    }
};
