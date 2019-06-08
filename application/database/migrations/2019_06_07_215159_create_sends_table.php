<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('certificate_id')->unsigned();
            $table->string('dni',120)->nullable();
            $table->string('first_name',60)->nullable();
            $table->string('last_name',60)->nullable();
            $table->string('email',250)->nullable(false);
            $table->timestamps();

            $table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sends');
    }
}
