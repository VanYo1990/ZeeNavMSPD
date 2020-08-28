<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Machine;

class ProjectsController extends Controller
{
    //
    //添加新施工项目
    public function store(Request $request)
    {
        
        if (!$request->machine_sn) {
            return response()->json([
                'error' => [
                    'message' => '设备编号不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        if (!$request->password) {
            return response()->json([
                'error' => [
                    'message' => '设备密码不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        if (!$request->name) {
            return response()->json([
                'error' => [
                    'message' => '项目名称不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        $sn = $request->machine_sn;
        $pwd = $request->password; 

        $machine = Machine::where('sn', '=',  $sn)->first();
        if($machine === null){
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '设备不存在null', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
                404);
        }
        if (\Hash::check($pwd,$machine->password) === false){
            //没有记录
            return response()->json([
                'error' => [
                    'message' => '设备不存在error psw', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
                404);
        }


        // //laravel 新增一条数据并返回 ID：https://blog.csdn.net/qq_27084325/article/details/107290823
        // $project = Project::create([
        //     'machine_sn' => $request->machine_sn,
        //     'name' => $request->name,
        //     'piles_number' => $request->piles_number ? $request->piles_number : 0,
        // ]);

        
        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'id' => 1,//$project->id,
                'name' => '测试项目',//$project->name,
                'creat_at' => '2020-08-26 22:05:24'//$project->created_at
            ]
        ])->setStatusCode(201);
    }
}
