<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('info', 500)->nullable();
            $table->string('category', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->float('price')->nullable();
            $table->integer('square')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('size', 255)->nullable();
            $table->string('condition', 255)->nullable();
            $table->string('construction', 255)->nullable();
            $table->string('zoning', 255)->nullable();
            $table->string('dimension', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
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
        Schema::dropIfExists('properties');
    }
}
