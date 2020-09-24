<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Auth\AuthenticationException;

use App\Models\Image;

use App\Models\UserMachines;
use App\Models\Machine;

use DB;

use Hash;


class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

       if (!$verifyData) {
           abort(403, '验证码已失效');
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => \Hash::make($request->password),//$request->password,
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        //return new UserResource($user);

        return (new UserResource($user))->showSensitiveFields();
    }

    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        //return new UserResource($request->user());

        return (new UserResource($request->user()))->showSensitiveFields();
    }

    public function update(UserRequest $request)
    {
        $user = $request->user();

        $attributes = $request->only(['name', 'email', 'introduction']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);

        return (new UserResource($user))->showSensitiveFields();
    }

    //管理员修改用户密码
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        //获取当前用户的权限，只有管理员和站长拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3){
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'no permission to operate',
                'data' => []
                ],
            404);
        }

        $id = $request->id;

        if ( !$request->id || !$request->password_new) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => '参数有误或缺少必要参数',
                'data' => [
                        'request' => $request
                    ]
                ],
            404);
        }

        //判断用户是否存在
        if (User::where('id', '=', $id)->exists() == FALSE) {
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => '用户不存在',
                'data' => []
                ],
                404);
        }

        //获取当前id数据
        $user_set = User::where('id', '=', $id)->first();

        $user_set->password = \Hash::make($request->password_new);
        
        $user_set->update();

        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'update password success',
            'data' => [
                'id' => $id
            ]
            
        ])->setStatusCode(201);
    }

    //用户修改自己的密码
    public function updateMyPassword(Request $request)
    {
        $user = $request->user();

        $id = $user->id;

        if ( !$request->password_old || !$request->password_new) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => '参数有误或缺少必要参数',
                'data' => [
                        'request' => $request
                    ]
                ],
            404);
        }

        //判断用户是否存在
        if (User::where('id', '=', $id)->exists() == FALSE) {
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => '用户不存在',
                'data' => []
                ],
                404);
        }

        //获取当前id数据
        $user_set = User::where('id', '=', $id)->first();

        if (!Hash::check($request->password_old , $user_set['password'])){
            //密码错误
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 密码错误',
                'data' => []
                ],
                404);
        }

        $user_set->password = \Hash::make($request->password_new);
        
        $user_set->update();

        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'update my password success',
            'data' => [
                'id' => $id
            ]
            
        ])->setStatusCode(201);
    }

    //小程序注册
    public function weappStore(UserRequest $request)
    {
        // 缓存中是否存在对应的 key
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            abort(403, '验证码已失效');
        }

        // 判断验证码是否相等，不相等反回 401 错误
        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            throw new AuthenticationException('验证码错误');
        }

        // 获取微信的 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($request->code);

        if (isset($data['errcode'])) {
            throw new AuthenticationException('code 不正确');
        }

        // 如果 openid 对应的用户已存在，报错403
        $user = User::where('weapp_openid', $data['openid'])->first();

        if ($user) {
            throw new AuthenticationException('微信已绑定其他用户，请直接登录');
        }

        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => \Hash::make($request->password),//$request->password,
            'weapp_openid' => $data['openid'],
            'weixin_session_key' => $data['session_key'],
        ]);

        return (new UserResource($user))->showSensitiveFields();
    }

    public function queryUsersAll(Request $request){
        //获取当前用户的权限，只有管理员和站长拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3){
            return response()->json([
                'error' => [
                    'message' => 'no permission to operate', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        //获取所有机器信息
        $users = User::all();

        foreach($users as $user) {
            $sn = array();

            //获取用户的权限信息
            $role_ids = array();
            $permission_ids = array();

            $ids = DB::table('model_has_roles')->where('model_id', $user->id)->get();

            foreach($ids as $id) {
            
                array_push($role_ids,$id->role_id);
            }

            $ids = DB::table('role_has_permissions')->whereIn('role_id', $role_ids)->get();

            foreach($ids as $id) {
            
                array_push($permission_ids,$id->permission_id);
            }

            $permission_names = DB::table('permissions')->whereIn('id', $permission_ids)->get('name');

            $user['permission'] = $permission_names;
            //获取用户的权限信息结束

            $machines = UserMachines::where('user_id','=',$user->id)->get();
            foreach($machines as $machine) {
            
                array_push($sn,$machine->machine_sn);
            }

            //根据多个ID检索Laravel Model结果，https://ask.csdn.net/questions/991777
            $return_machines = Machine::whereIn('sn', $sn)->get();

            $user['machines'] = $return_machines;
        }

        return response()->json([
            'users' => $users,
        ])->setStatusCode(201);
    }

    //管理员添加新用户
    public function addUser(Request $request){
        
        if (!$request->phone) {
            return response()->json([
                'error' => [
                    'message' => '手机号不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }
        
        if (!$request->name) {
            return response()->json([
                'error' => [
                    'message' => '姓名不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }
        
        if (!$request->password) {
            return response()->json([
                'error' => [
                    'message' => '密码不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        //获取当前用户的权限，只有管理员和站长拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3){
            return response()->json([
                'error' => [
                    'message' => 'no permission to operate', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }
        

        $user = User::where('phone', '=',  $request->phone)->first();
        if ($user){
            //已存在
            return response()->json([
                'error' => [
                    'message' => '用户已存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
                404);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => \Hash::make($request->password),//$request->password,
        ]);

        return response()->json([
            'users' => $user,
        ])->setStatusCode(201);
    }

    //
    public function destroy($id, Request $request)
    {
        
        //获取当前用户的权限，只有管理员和站长拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3){
            return response()->json([
                'error' => [
                    'message' => 'no permission to operate', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        //判断是否存在
        $user = User::where('id', '=', $id)->first();
        if ($user === null){
            //不存在记录
            return response()->json([
                'error' => [
                    'message' => '用户不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }


        $user->delete();

        // deleted来记录删除了多少条数据
        $deleted = UserMachines::where('user_id','=',$id)->delete();

        return response(null, 204);
    }

}
