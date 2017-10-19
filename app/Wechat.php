<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wechat extends Model
{
    protected $table = "wechat_users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wxid', 'jaccount',
    ];

    public function jaccount_info() {
        return $this->hasOne('App\Jaccount', 'account_name', 'jaccount');
    }

}
