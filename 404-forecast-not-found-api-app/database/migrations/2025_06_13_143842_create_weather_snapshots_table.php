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
        Schema::create('weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->float('temperature');
            $table->float('feels_like');
            $table->integer('pressure');
            $table->integer('humidity');
            $table->float('wind_speed');
            $table->integer('wind_deg');
            $table->integer('clouds');
            $table->string('weather_main');
            $table->string('weather_description');
            $table->string('weather_icon');
            $table->timestamp('sunrise')->nullable();
            $table->timestamp('sunset')->nullable();
            $table->enum('data_source', ['current', 'historical']);
            $table->timestamp('captured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_snapshots');
    }
};
