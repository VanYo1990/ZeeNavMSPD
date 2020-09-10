<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Machine;
use Hash;

class MachinesController extends Controller
{
    //添加新设备
    public function store(Request $request)
    {
        
        if (!$request->sn) {
            return response()->json([
                'error' => [
                    'message' => '设备编号不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }
        
        if (!$request->contact_name) {
            return response()->json([
                'error' => [
                    'message' => '联系人姓名不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }
        
        if (!$request->contact_phone) {
            return response()->json([
                'error' => [
                    'message' => '联系人电话不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        if (!$request->name) {
            return response()->json([
                'error' => [
                    'message' => '设备名称不能为空', 
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
        
        $sn = $request->sn;
        //$machines = DB::table('machines')->where('sn',$sn)->get();
        $machine = Machine::where('sn', '=',  $sn)->first();
        // if (Machine::where('sn', '=', $sn)->exists()) {
        if ($machine){
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '设备已存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
                404);
        }

        //laravel 新增一条数据并返回 ID：https://blog.csdn.net/qq_27084325/article/details/107290823
        $machine = Machine::create([
            'sn' => $request->sn,
            'password' => \Hash::make($request->password),
            'content' => json_encode([
                'contact_phone' => $request->contact_phone,
                'contact_name' => $request->contact_name,
                'name' => $request->name,
                'latitude' => '0.0000000',
				'longitude' => '0.0000000'
            ])
        ]);

        
        return response()->json([
            'id' => $machine->id,
            'sn' => $machine->sn,
            'content' => $machine->content,
            'created_at' => $machine->created_at
        ])->setStatusCode(201);
    }

    //
    public function update($id, Request $request)
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

        //判断当前设备是否存在
        if (Machine::where('id', '=', $id)->exists() == FALSE) {
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '设备不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
                404);
        }

        //获取当前id数据
        $machine = Machine::where('id', '=', $id)->first();

        $attributes = $request->only(['sn', 'content']);

        $attributes['password'] = \Hash::make($request->password);

        $machine->update($attributes);

        return response()->json([
            'message' => 'update success',
            'id' => $id,
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
        $machine = Machine::where('id', '=', $id)->first();
        if ($machine === null){
            //存在相同记录
            return response()->json([
                'error' => [
                    'message' => '设备不存在', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }


        $machine->delete();

        // deleted来记录删除了多少条数据
        $deleted = UserMachines::where('machine_sn','=',$machine->sn)->delete();

        return response(null, 204);
    }

    public function queryMachinesAll(Request $request){
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
        $machines = Machine::all();

        return response()->json([
            'machines' => $machines,
        ])->setStatusCode(201);
    }

    //机器登录
    public function login(Request $request)
    {
        if (!$request->sn) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备编号不能为null',
                'data' => []
                ],
                404);
        }
        
        if (!$request->psw) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 密码不能为null',
                'data' => []
                ],
                404);
        }
        
        //判断用户输入的密码与数据库的密码是否一致
        // 表单中的密码：$req->password   （原始）
        // 数据库的密码：$user->password （哈希之后 ）
        // laravel中 Hash::check(原始，哈希之后)判断是否一致
        $sn = $request->sn;
        $psw = \Hash::make($request->psw);
        //$machines = DB::table('machines')->where('sn',$sn)->get();
        $machine = Machine::where('sn', '=',  $sn)->first();
        // if (Machine::where('sn', '=', $sn)->exists()) {
        if (!Hash::check($request->psw , $machine['password'])){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 登录失败，编号或密码错误',
                'data' => []
                ],
                404);
        }

        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success: 登录成功',
            'data' => []
        ])->setStatusCode(201);
    }
    
}
