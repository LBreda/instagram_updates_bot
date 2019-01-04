<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo', function (Blueprint $table) {
            $table->increments('id');
            $table->text('data');
            $table->integer('type_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('todo', function (Blueprint $table) {
            $table->foreign('type_id')->references('id')->on('todo_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todo', function (Blueprint $table) {
            $table->dropForeign($table->getTable() . '_' . 'todo_types' . '_foreign');
        });

        Schema::dropIfExists('todo');
    }
}
