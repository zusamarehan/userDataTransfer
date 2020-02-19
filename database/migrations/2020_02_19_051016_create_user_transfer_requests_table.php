<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransferRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transfer_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('percentage')->default(0);
            $table->integer('project_transferred')->default(0);
            $table->integer('task_transferred')->default(0);
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();

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
        Schema::dropIfExists('user_transfer_requests');
    }
}
