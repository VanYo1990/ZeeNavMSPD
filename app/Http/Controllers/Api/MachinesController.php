<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Machine;

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
    
}
