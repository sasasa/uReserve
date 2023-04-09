<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('postal_code')->index()->comment('郵便番号');
            $table->string('prefecture')->index()->comment('都道府県');
            $table->string('city')->index()->comment('市町村');
            $table->string('street')->index()->comment('町域');
            $table->string('block')->nullable()->comment('丁目');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
};
