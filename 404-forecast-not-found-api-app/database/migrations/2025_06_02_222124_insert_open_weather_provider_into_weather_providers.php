<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('weather_providers')->insert([
            'name' => 'Open Weather',
            'description' => 'Open Weather Map Service Provider',
            'code' => 'openweathermap',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('weather_providers')
            ->where('code', 'openweathermap')
            ->delete();
    }
};
