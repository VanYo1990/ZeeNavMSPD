<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Machine;
use App\Models\UserMachines;
use App\Models\WorkRecords;

class ProjectsController extends Controller
{
    //
    //添加新施工项目
    public function store(Request $request)
    {
        
        if (!$request->machine_sn) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备编号不能为空',
                'data' => []
                ],
            404);
        }

        if (!$request->password) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备密码不能为空',
                'data' => []
                ],
            404);
        }

        if (!$request->name) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 项目名不能为空',
                'data' => []
                ],
            404);
        }

        // if (!$request->identify_code) {
        //     return response()->json([
        //         'resultCode' => 101,
        //         'resultMessage' => 'error message: 识别码不能为空',
        //         'data' => []
        //         ],
        //     404);
        // }

        $sn = $request->machine_sn;
        $pwd = $request->password; 

        $machine = Machine::where('sn', '=',  $sn)->first();
        if($machine === null){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备不存在 error name',
                'data' => []
                ],
                404);
        }
        if (\Hash::check($pwd,$machine->password) === false){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备不存在 error psw',
                'data' => []
                ],
                404);
        }

        $machine = Project::where('machine_sn', '=',  $sn)->where('name', '=',  $request->name)->first();
        if($machine){
            //已经有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 项目信息已存在',
                'data' => []
                ],
                404);
        }


        //laravel 新增一条数据并返回 ID：https://blog.csdn.net/qq_27084325/article/details/107290823
        $project = Project::create([
            'machine_sn' => $request->machine_sn,
            'name' => $request->name,
            'piles_number' => $request->piles_number ? $request->piles_number : 0,
        ]);

        
        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'id' => $project->id,
                'name' => $project->name,
                'creat_at' => $project->created_at
            ]
        ])->setStatusCode(201);
    }

    //用户获取某设备的所有施工项目信息
    public function getAllbysn($sn,Request $request)
    {
        $user = $request->user();
        
        if (!$sn) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备编号不能为空',
                'data' => []
                ],
            404);
        }

        $machine = UserMachines::where('user_id', '=',  $user->id)->where('machine_sn', '=',  $sn)->first();
        if($machine === null){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 用户不存在该设备 error name',
                'data' => []
                ],
                404);
        }

        $projects = Project::where('machine_sn', '=',  $sn)->get();
        if(!$projects){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 没有项目信息',
                'data' => []
                ],
                404);
        }
        
        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'projects' => $projects
            ]
        ])->setStatusCode(201);
    }

    //用户获取某施工项目详情信息,$id为项目id
    public function getProjectdetails($sn,$id,Request $request)
    {
        $user = $request->user();
        
        if (!$sn) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 设备编号不能为空',
                'data' => []
                ],
            404);
        }

        if (!$id) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 项目编号不能为空',
                'data' => []
                ],
            404);
        }

        $machine = UserMachines::where('user_id', '=',  $user->id)->where('machine_sn', '=',  $sn)->first();
        if($machine === null){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 用户不存在该设备 error name',
                'data' => []
                ],
                404);
        }

        $workRecords = WorkRecords::where('project_id', '=',  $id)->get();
        if(!$workRecords){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 没有项目详情信息',
                'data' => []
                ],
                404);
        }
        
        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'projects' => $workRecords
            ]
        ])->setStatusCode(201);
    }
}
