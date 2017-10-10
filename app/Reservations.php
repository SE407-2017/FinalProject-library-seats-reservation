<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservations extends Model {

    protected $table = 'reservations';
    protected $fillable = array('name', 'jaccount', 'floor_id', 'table_id', 'seat_id', 'arrive_at', 'is_arrived', 'is_left');


}
