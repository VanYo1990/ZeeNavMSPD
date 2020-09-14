<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UserMachines;

use App\Models\User;
use App\Models\Machine;


class UserMachinesController extends Controller
{
    //添加用户设备
    public function store(Request $request)
    {
        
        if (!$request->user_id) {
            throw new AuthenticationException('参数错误');
        }
        
        if (!$request->machine_sn) {
            throw new AuthenticationException('参数错误');
        }
        
        $id = $request->user_id;
        //$users = DB::table('users')->where('id',$id)->get();
        // if ($users->first()) {//若无数据，打印出来为null
        //      //
        //     throw new AuthenticationException('用户不存在');
        // } 
        //Laravel Eloquent：判断数据是否存在，https://learnku.com/laravel/wikis/27722
        $users = User::where('id', '=',  $id)->first();
        if ($users === null){
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '用户不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }
        
        
        $sn = $request->machine_sn;
        //$machines = DB::table('machines')->where('sn',$sn)->get();
        if (Machine::where('sn', '=', $sn)->exists() == FALSE) {
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '设备不存在', 
                    'type' => 'Exception',
                    'code' => '0'
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

        $user_machines = UserMachines::where('user_id', '=',  $id)->where('machine_sn', '=', $sn)->first();
        if ($user_machines){
            //存在相同记录
            return response()->json([
                'error' => [
                    'message' => '存在相同的记录', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        

        //laravel 新增一条数据并返回 ID：https://blog.csdn.net/qq_27084325/article/details/107290823
        $userMachine = UserMachines::create([
            'user_id' => $request->user_id,
            'machine_sn' => $request->machine_sn,
            'verified_at' => now(),
        ]);

        
        return response()->json([
            'id' => $userMachine->id,
            'user_id' => $userMachine->user_id,
            'machine_sn' => $userMachine->machine_sn,
            'verified_at' => $userMachine->verified_at,
            'creat_at' => $userMachine->created_at,
        ])->setStatusCode(201);
    }

    //
    public function update(UserMachines $usermachine, Request $request)
    {
        $user = $request->user();

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

        //判断当前用户是否存在
        $users = User::where('id', '=',  $request->user_id)->first();
        if ($users === null){
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '用户不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        //判断当前设备是否存在
        if (Machine::where('sn', '=', $request->machine_sn)->exists() == FALSE) {
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '设备不存在', 
                    'type' => 'Exception',
                    'code' => $usermachine
                    ]
                ],
                404);
        }

        //判断是否存在
        $user_machines = UserMachines::where('user_id', '=',  $request->user_id)->where('machine_sn', '=', $request->machine_sn)->first();
        if ($user_machines){
            //存在相同记录
            return response()->json([
                'error' => [
                    'message' => '存在相同的记录', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        $attributes = $request->only(['user_id', 'machine_sn']);

        $usermachine->update($attributes);

        return response()->json([
            'message' => 'update success',
            'user_id' => $request->user_id,
            'machine_sn' => $request->machine_sn,
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
        $usermachine = UserMachines::where('id', '=',  $id)->first();
        if ($usermachine === null){
            //存在相同记录
            return response()->json([
                'error' => [
                    'message' => '记录不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }


        $usermachine->delete();

        return response(null, 204);
    }

    //获取所有用户信息
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

        return response()->json([
            'users' => $users,
        ])->setStatusCode(201);
    }

    //获取某用户所属的机器
    public function queryMachineMe($id, Request $request){
        //获取当前用户的权限，只有管理员和站长拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3 && $request->user()->id != $id){

            return response()->json([
                'error' => [
                    'message' => 'no permission to operate', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        //判断当前用户是否存在
        $users = User::where('id', '=',  $id)->first();
        if ($users === null){
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '用户不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        //获取id为$id的所有机器信息
        //https://www.jb51.net/article/172651.htm
        // $return_machines = Machine::join('user_machines', 'user_machines.machine_sn','=','machines.sn')
        //                     ->where('user_machines.user_id','=',$id)
        //                     ->get();
        
        $sn = array();

        $users = UserMachines::where('user_id','=',$id)->get();
        foreach($users as $user) {
           
            array_push($sn,$user->machine_sn);
        }

        //根据多个ID检索Laravel Model结果，https://ask.csdn.net/questions/991777
        $return_machines = Machine::whereIn('sn', $sn)->get();
        
        return response()->json([
            'id' => $id,
            'machines' => $return_machines,
        ])->setStatusCode(201);
    }

}
