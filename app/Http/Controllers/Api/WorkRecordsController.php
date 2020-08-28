<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\WorkRecords;
use App\Models\Machine;

class WorkRecordsController extends Controller
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

        if (!$request->project_id) {
            return response()->json([
                'error' => [
                    'message' => '项目id不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        if (!$request->pile_name) {
            return response()->json([
                'error' => [
                    'message' => '桩位名称不能为空', 
                    'type' => 'Exception',
                    'code' => ''
                    ]
                ],
            404);
        }

        if (!$request->content) {
            return response()->json([
                'error' => [
                    'message' => '施工信息不能为空', 
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

        $workrecord = WorkRecords::where('machine_sn', '=',  $sn)
                                ->where('project_id', '=',  $request->project_id)
                                ->where('pile_name', '=',  $request->pile_name)
                                ->first();

        if($workrecord){
            //存在，修改

            $attributes = $request->only(['machine_sn', 'project_id', 'pile_name']);

            $attributes['content'] = $request->content;

            $workrecord->update($attributes);
        }
        else{
            //不存在，新增

            //laravel 新增一条数据并返回 ID：https://blog.csdn.net/qq_27084325/article/details/107290823
            $workrecord = WorkRecords::create([
                'machine_sn' => $request->machine_sn,
                'project_id' => $request->project_id,
                'pile_name' => $request->pile_name,
                'content' => $request->content,
            ]);
        }
        
        return response()->json([
            'id' => $workrecord->id,
            'content' => json_decode($workrecord->content),
            'creat_at' => $workrecord->created_at
        ])->setStatusCode(201);
    }
}
