<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservations extends Model {

    protected $table = 'reservations';
    protected $fillable = array('name', 'jaccount', 'floor_id', 'table_id', 'seat_id', 'arrive_at', 'is_arrived', 'is_left');

    public function jaccount_info() {
        return $this->hasOne('App\Jaccount', 'account_name', 'jaccount');
    }

    public function floor() {
        return $this->hasOne('App\Floors', 'id', 'floor_id');
    }

}
