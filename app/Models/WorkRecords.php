<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkRecords extends Model
{
    protected $table = 'work_records';

    //
	protected $fillable = [
        'machine_sn',
        'project_id',
        'pile_name',
        'content',
    ];

    //laravel 手动获取created_at update_at为对象，获取不到的解决办法
    //获取到的结果为"2019-05-15T14:09:01.000000Z"很奇怪
    //https://www.jianshu.com/p/9d85d0f48ae7
    protected $casts = array('created_at' => 'created_at','updated_at'=>'updated_at');
    
}
