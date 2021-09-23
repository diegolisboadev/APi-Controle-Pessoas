<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlePessoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controle_pessoas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome')->unique();
            $table->string('sexo');
            $table->string('peso');
            $table->string('altura');
            $table->string('imc');
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
        Schema::dropIfExists('controle_pessoas');
    }
}
