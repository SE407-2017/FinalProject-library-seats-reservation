<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tables extends Model {

    protected $table = 'data_tables';
    protected $fillable = ['floor_id', 'seats_count', 'has_socket'];

    public function floor() {
        return $this->hasOne('App\Floors', 'id', 'floor_id');
    }

}
