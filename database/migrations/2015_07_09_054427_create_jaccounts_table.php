<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJaccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jaccounts', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('account_name');
            $table->string('real_name');
            $table->string('department');
            $table->string('student_number');
            $table->boolean('is_student');
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
        Schema::drop('jaccounts');
    }
}
