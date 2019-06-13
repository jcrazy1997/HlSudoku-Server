<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->default('');
            $table->string('open_id')->unique()->nullable(false);
            $table->string('session_key')->unique()->nullable(false);
            $table->integer('score_time')->nullable(true);
            $table->integer('score_step')->nullable(true);
            $table->string('api_token')->default('');
            $table->string('avatar_url')->default('');
            $table->string('nickname')->default('');
            $table->float('degree')->default(0.0);
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
        Schema::dropIfExists('score');
    }
}
