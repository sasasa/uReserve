<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->comment('ユーザーID');
            $table->foreignId('event_id')->constrained()->onUpdate('cascade')->comment('イベントID');
            $table->integer('number_of_people')->comment('参加人数');
            $table->datetime('canceled_date')->nullable()->comment('キャンセル日時');
            $table->timestamps();
            $table->collation = 'utf8mb4_bin';
        });
        DB::statement("ALTER TABLE reservations COMMENT '予約テーブル';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
