<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//use Encore\Admin\Traits\DefaultDatetimeFormat;

class Machine extends Model
{
    // 加上这个 Trait，修改 Laravel 7 默认的日期展示格式
    //use DefaultDatetimeFormat;

    //
    //
	protected $fillable = [
        'sn',
        'password',
        'content',
    ];

    //laravel 手动获取created_at update_at为对象，获取不到的解决办法
    //获取到的结果为"2019-05-15T14:09:01.000000Z"很奇怪
    //https://www.jianshu.com/p/9d85d0f48ae7
    protected $casts = array('created_at' => 'created_at','updated_at'=>'updated_at');
    
    
    public function isOnline()
    {
        return Cache::has('machine-is-online-' . $this->id);
    }

    //多对多关联
    function users()
     {
         //laravel 多对多，https://www.jianshu.com/p/6b0f909c31b2
         //Laravel 之多对多的关系模型，https://learnku.com/articles/28430
         //第二个参数是表名，数据库里那个表 叫啥，他就叫啥，第三个参数是本类的字段，第四个参数是要查找的字段
         return $this->belongsToMany('App\Models\User', 'user_machines', 'machine_sn', 'user_id')
                             ->withPivot('verified_at')->withTimestamps();
     }
}
