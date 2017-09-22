<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDataTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('floor_id');
            $table->integer('seats_count');
            $table->boolean('has_socket');
            $table->timestamps();
        });
        for ($f = 0; $f < 3; $f++) {
            for ($i = 0; $i < 20; $i++) {
                DB::table('data_tables')->insert(
                    array(
                        'floor_id' => $f+1,
                        'seats_count' => 4,
                        'has_socket' => random_int(0, 1),
                    )
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
