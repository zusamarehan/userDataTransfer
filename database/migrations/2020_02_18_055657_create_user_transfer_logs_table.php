<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transfer_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('module', 100);
            $table->integer('module_id');
            $table->integer('from_user_id');
            $table->string('from_user_name', 100);
            $table->integer('to_user_id');
            $table->string('to_user_name', 100);

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
        Schema::dropIfExists('user_transfer_logs');
    }
}
