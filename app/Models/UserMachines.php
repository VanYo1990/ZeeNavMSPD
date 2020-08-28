<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class UserMachines extends Model
{
    protected $table = 'user_machines';

    //
	protected $fillable = [
        'user_id',
        'machine_sn',
        'verified_at',
    ];
    protected $dates = ['verified_at'];

    //laravel 手动获取created_at update_at为对象，获取不到的解决办法
    //获取到的结果为"2019-05-15T14:09:01.000000Z"很奇怪
    //https://www.jianshu.com/p/9d85d0f48ae7
    protected $casts = array('verified_at' => 'verified_at','created_at' => 'created_at','updated_at'=>'updated_at');

    public function user()
    {
        return $this->belongsTo(User::class);
    }
	
	// public function machines()
    // {
    //     return $this->belongsToMany(Machine::class);
    // }
}
