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
        Schema::create('air_quality_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('aqi');
            $table->float('co');
            $table->float('no');
            $table->float('no2');
            $table->float('o3');
            $table->float('so2');
            $table->float('pm2_5');
            $table->float('pm10');
            $table->float('nh3');
            $table->timestamp('captured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('air_quality_readings');
    }
};
