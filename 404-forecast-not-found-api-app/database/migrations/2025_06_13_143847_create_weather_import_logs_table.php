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
        Schema::create('weather_import_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['current', 'forecast', 'air']);
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('success');
            $table->text('error_message')->nullable();
            $table->timestamp('pulled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_import_logs');
    }
};
