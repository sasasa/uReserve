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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('イベント名');
            $table->text('information')->comment('イベント詳細');
            $table->integer('max_people')->comment('イベント参加人数');
            $table->datetime('start_date')->comment('イベント開始日時');
            $table->datetime('end_date')->comment('イベント終了日時');
            $table->boolean('is_visible')->comment('表示するかどうか');
            $table->timestamps();
            $table->collation = 'utf8mb4_bin';
        });
        DB::statement("ALTER TABLE events COMMENT 'イベントテーブル';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
