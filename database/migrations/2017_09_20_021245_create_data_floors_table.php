<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDataFloorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_floors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('floor_name');
            $table->timestamps();
        });
        DB::table('data_floors')->insert(
            array(
                'floor_name' => '一楼',
            )
        );
        DB::table('data_floors')->insert(
            array(
                'floor_name' => '二楼',
            )
        );
        DB::table('data_floors')->insert(
            array(
                'floor_name' => '三楼',
            )
        );
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
