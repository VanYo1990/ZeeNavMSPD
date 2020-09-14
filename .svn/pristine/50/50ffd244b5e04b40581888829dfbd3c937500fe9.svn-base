<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

use App\Models\Machine;


class User extends Authenticatable implements MustVerifyEmailContract, JWTSubject
{
    use HasRoles;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //'name', 'email', 'password',
        
        //修改 user 模型的 fillable 未设置 phone，不然 phone 属性无法正确保存
        'name', 'phone', 'email', 'password', 'introduction', 'avatar',
        'weixin_openid', 'weixin_unionid', 'registration_id',
        'weixin_session_key', 'weapp_openid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',

        //屏蔽敏感信息
        'password', 'remember_token', 'weixin_openid', 'weixin_unionid'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    //多对多关联
    function machines()
    {
        //laravel 多对多，https://www.jianshu.com/p/6b0f909c31b2
        //Laravel 之多对多的关系模型，https://learnku.com/articles/28430
        //第二个参数是表名，数据库里那个表 叫啥，他就叫啥，第三个参数是本类的字段，第四个参数是要查找的字段
        //第一个参数是 第二个Model
        //第二个参数是 关系表名
        //第三个参数是 第一个Model在关系表中的外键ID
        //第四个参数是 第二个Model在关系表中的外键ID
        // return $this->belongsToMany('App\Models\Machine', 'user_machines', 'user_id', 'machine_sn')
        //                     ->withPivot('verified_at')->withTimestamps();
        return $this->belongsToMany(Machine::class, 'user_machines', 'user_id', 'machine_sn')
                                ->withPivot('verified_at')->withTimestamps();
        
    }
}
