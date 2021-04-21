<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('longtext');
            $table->bigInteger('big_increments');
            $table->enum('enum',['1','2']);
            $table->unsignedBigInteger('unsignedInteger');
            $table->float('float');
            $table->time('time');
            $table->boolean('boolean');
            $table->tinyInteger('unsignedTinyInteger');
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
        Schema::dropIfExists('types');
    }
}
