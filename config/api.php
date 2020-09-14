<?php

return [
    /*
     * 接口频率限制
     */
    'rate_limits' => [
        // 访问频率限制，次数/分钟
        'access' =>  env('RATE_LIMITS', '600,1'),
        // 登录相关，次数/分钟
        'sign' =>  env('SIGN_RATE_LIMITS', '200,1'),
        // 数据记录相关，次数/分钟
        'record' =>  env('RECORD_RATE_LIMITS', '6000,1'),
    ],
];
