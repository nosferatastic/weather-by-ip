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
        Schema::create(config('weatherbyip.tables.location_lookups', 'location_lookups'), function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->text('location');
            $table->float('lat',5,2);
            $table->float('lon',5,2);
            $table->datetime('lookup_time');
            $table->timestamps();
        });
        Schema::create(config('weatherbyip.tables.forecasts', 'forecasts'), function (Blueprint $table) {
            $table->id();
            $table->bigInteger('location_lookup_id')->unsigned();
            $table->foreign('location_lookup_id')->references('id')->on(config('weatherbyip.tables.location_lookups', 'location_lookups'))->onDelete('cascade');
            $table->integer('weather_code');
            $table->datetime('forecast_date');
            $table->float('temp_high',3,1);
            $table->float('temp_low',3,1);
            $table->timestamps();
            $table->index('location_lookup_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('weatherbyip.tables.location_lookups'));
        Schema::dropIfExists(config('weatherbyip.tables.forecasts'));
    }
};
