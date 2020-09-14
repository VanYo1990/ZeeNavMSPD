<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Machine;

class DataRecordsController extends Controller
{
    //
    //添加新数据记录，记录设备上传的位置信息
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

        if (!$request->content) {
            return response()->json([
                'resultCode' => 101,
                'resultMessage' => 'error message: 记录信息不能为空',
                'data' => []
                ],
            404);
        }

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

        $json_content = json_decode($machine->content);
        $request_content = json_decode($request->content);

        $json_content->latitude = $request_content->latitude;
        $json_content->longitude = $request_content->longitude;

        //获取当前id数据
        $attributes = $request->only(['content']);

        $attributes['content'] = json_encode($json_content);

        $machine->update($attributes);
        
        return response()->json([
            'resultCode' => 201,
            'resultMessage' => 'success',
            'data' => [
                'content' => $machine->content,
                'creat_at' => $machine->created_at
            ]
        ])->setStatusCode(201);
    }
}
