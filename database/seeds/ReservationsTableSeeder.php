<?php

use Illuminate\Database\Seeder;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<500;$i++){
            $timeStampInt= rand(1504224000,1516406399); //unix timestamp
            \App\Reservations::create([
                'name'   => 'Name '.$i,
                'jaccount'    => 'Jaccount '.$i,
                'floor_id' => rand(1,3),
                'table_id' => rand(1,60),
                'seat_id' => rand(1,4),
                'arrive_at' => date("Y-m-d H:i:s",$timeStampInt),
                'is_arrived' => rand(0,1),
                'is_left' => rand(0,1),
            ]);

        }
    }
}
