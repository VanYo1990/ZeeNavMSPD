<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Machine;
use App\Models\UserMachines;
use App\Models\WorkRecords;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;


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

        //获取当前用户的权限，管理员和站长同样拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3){
            //非管理员用户,检查用户是否拥有该设备
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

        //获取当前用户的权限，管理员和站长同样拥有权限
        $permissions = $request->user()->getAllPermissions();
        if($permissions->count() != 1 && $permissions->count() != 3){
            //非管理员用户,检查用户是否拥有该设备
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
    
    //用户获取自己所有设备的所有施工详情信息（统计信息）,$id为用户id
    public function getAllProjectdetails_User($id,$day,Request $request)
    {
        $user = $request->user();
        
        if (!$id) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 用户id号不能为空',
                'data' => []
                ],
            404);
        }

        if (!$day) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 日期天数不能为空',
                'data' => []
                ],
            404);
        }

        $machines = UserMachines::where('user_id', '=',  $user->id)->get();
        if($machines === null){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 用户不存在设备 error name',
                'data' => []
                ],
                404);
        }

        // //如何使用Laravel Eloquent创建多个Where子句查询
        // //https://blog.csdn.net/w36680130/article/details/106854104
        // foreach ($machines as $machine) {
        //     $workRecords = WorkRecords::where('machine_sn','=',$machine->machine_sn)->get();
        // }

        // //$workRecords = WorkRecords::where($where)->get();
        // if(!$workRecords){
        //     //没有记录
        //     return response()->json([
        //         'resultCode' => 101,
        //         'resultMessage' => 'error message: 没有项目详情信息',
        //         'data' => []
        //         ],
        //         404);
        // }

        if($day < 1){
            $day = 1;
        }

        $time = (int)$day;
        // foreach ($machines as $machine) {
        //     $data = workRecords::where('updated_at','<', Carbon::now())
        //         ->where('updated_at','>', $time > 1 ? Carbon::today()->subDays($time) : Carbon::today())
        //         ->where('machine_sn','=',$machine->machine_sn)
        //         ->select([$time > 1 ? DB::raw('DATE(updated_at) as time') : DB::raw('DATE_FORMAT(updated_at,\'%H\') as time'), DB::raw('COUNT("*") as count')])
        //         ->groupBy('time')
        //         ->get();
        // }

        //laravel where中多条件查询
        //https://www.cnblogs.com/mitang/p/4928059.html
        $where = [];
        foreach ($machines as $machine) {
            $where[] = array('machine_sn', '=', $machine->machine_sn);   // 关键是这里
        }

        $data = workRecords::where('updated_at','<', Carbon::now())
                ->where('updated_at','>', $time > 1 ? Carbon::today()->subDays($time) : Carbon::today())
                ->orwhere($where)
                ->select([$time > 1 ? DB::raw('DATE(updated_at) as time') : DB::raw('DATE_FORMAT(updated_at,\'%H\') as time'), DB::raw('COUNT("*") as count')])
                ->groupBy('time')
                ->get();    

        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'workRecords' => $data
            ]
        ])->setStatusCode(201);
    }

    //【笔记2】laravel数据统计绘图（今天、7天、30天数据）
    // https://segmentfault.com/a/1190000015825085?utm_source=tag-newest
    // public function getTimingHistory($time)
    // {
    //     $time = (int)$time;
    //     $data = StatsPlanClick::where('created_at','<', Carbon::now())
    //             ->where('created_at','>', $time > 1 ? Carbon::today()->subDays($time)Carbon::today())
    //             ->select([$time > 1 ? DB::raw('DATE(created_at) as time') : DB::raw('DATE_FORMAT(created_at,\'%H\') as time'), DB::raw('COUNT("*") as count')])
    //             ->groupBy('time')
    //             ->get();
    //     }
    //     return $this->successWithData($data);
    // }


    //用户获取自己某台设备的施工记录统计信息,$id为用户id
    public function getOneMachineWorkRecordsAccount_User($sn,$day,Request $request)
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

        if (!$day) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 日期天数不能为空',
                'data' => []
                ],
            404);
        }

        $machines = UserMachines::where('user_id', '=',  $user->id)->get();
        if($machines === null){
            //没有记录
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 用户不存在设备 error name',
                'data' => []
                ],
                404);
        }

        // //如何使用Laravel Eloquent创建多个Where子句查询
        // //https://blog.csdn.net/w36680130/article/details/106854104
        // foreach ($machines as $machine) {
        //     $workRecords = WorkRecords::where('machine_sn','=',$machine->machine_sn)->get();
        // }

        // //$workRecords = WorkRecords::where($where)->get();
        // if(!$workRecords){
        //     //没有记录
        //     return response()->json([
        //         'resultCode' => 101,
        //         'resultMessage' => 'error message: 没有项目详情信息',
        //         'data' => []
        //         ],
        //         404);
        // }

        if($day < 1){
            $day = 1;
        }

        $time = (int)$day;
        foreach ($machines as $machine) {
            $data = workRecords::where('updated_at','<', Carbon::now())
                ->where('updated_at','>', $time > 1 ? Carbon::today()->subDays($time) : Carbon::today())
                ->where('machine_sn','=',$sn)
                ->select([$time > 1 ? DB::raw('DATE(updated_at) as time') : DB::raw('DATE_FORMAT(updated_at,\'%H\') as time'), DB::raw('COUNT("*") as count')])
                ->groupBy('time')
                ->get();
        }
        

        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'workRecords' => $data
            ]
        ])->setStatusCode(201);
    }

}
