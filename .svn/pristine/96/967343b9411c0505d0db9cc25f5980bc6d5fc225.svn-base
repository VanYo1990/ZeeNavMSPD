<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::prefix('v1')->name('api.v1.')->group(function() {
//     Route::get('version', function() {
//         abort(403, 'test');
//         return 'this is version v1';
//     })->name('version');
// });

// Route::prefix('v2')->name('api.v2.')->group(function() {
//     Route::get('version', function() {
//         return 'this is version v2';
//     })->name('version');
// });


Route::prefix('v1')
    ->namespace('Api')
    ->name('api.v1.')
    ->group(function () {

    /*
    * 我们的 VerificationCodesController 放在了 Api 目录中，
    * 所以还需要调整一下统一的命名空间，使用 namespace 方法即可。
    * 
    * 这样所有 v1 版本的路由都会默认使用 Api 目录中的控制器，
    * 你还可以根据版本继续细分到 v1 ，v2 目录中。
    */
    Route::middleware('throttle:' . config('api.rate_limits.sign'))
            ->group(function () {
                // 图片验证码
                Route::post('captchas', 'CaptchasController@store')
                ->name('captchas.store');
                // 短信验证码
                Route::post('verificationCodes', 'VerificationCodesController@store')
                    ->name('verificationCodes.store');
                // 用户注册
                Route::post('users', 'UsersController@store')
                    ->name('users.store');

                /*
                 * 注意这里的参数，我们对 social_type 进行了限制，只会匹配 weixin，
                 * 如果你增加了其他的第三方登录，可以在这里增加限制，
                 * 例如支持微信及微博：->where('social_type', 'weixin|weibo')
                 */
                // 第三方登录
                Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
                    ->where('social_type', 'weixin')
                    ->name('socials.authorizations.store');
                // 登录
                Route::post('authorizations', 'AuthorizationsController@store')
                    ->name('api.authorizations.store');
                // 刷新token
                Route::put('authorizations/current', 'AuthorizationsController@update')
                    ->name('authorizations.update');
                // 删除token
                Route::delete('authorizations/current', 'AuthorizationsController@destroy')
                    ->name('authorizations.destroy');

                // 小程序登录
                Route::post('weapp/authorizations', 'AuthorizationsController@weappStore')
                ->name('weapp.authorizations.store');
                // 小程序注册
                Route::post('weapp/users', 'UsersController@weappStore')
                    ->name('weapp.users.store');
            });

    Route::middleware('throttle:' . config('api.rate_limits.access'))
        ->group(function () {
            // 游客可以访问的接口

            // 某个用户的详情
            Route::get('users/{user}', 'UsersController@show')
                ->name('users.show');
            // 分类列表
            Route::get('categories', 'CategoriesController@index')
                ->name('categories.index');

            // 机器添加施工项目信息
            Route::post('projects', 'ProjectsController@store')
            ->name('projects.store');
            // 机器添加施工记录信息
            Route::post('wordrecord', 'WorkRecordsController@store')
            ->name('wordrecord.store');

            
            // 登录后可以访问的接口
            Route::middleware('auth:api')->group(function() {
                // 当前登录用户信息
                Route::get('user', 'UsersController@me')
                    ->name('user.show');
                // 上传图片
                Route::post('images', 'ImagesController@store')
                    ->name('images.store');
                // 编辑登录用户信息
                Route::patch('user', 'UsersController@update')
                    ->name('user.patch');
                Route::put('user', 'UsersController@update')
                    ->name('user.update');
                // 当前登录用户权限
                Route::get('user/permissions', 'PermissionsController@index')
                ->name('user.permissions.index');

                // 管理员添加新用户
                Route::post('useradd', 'UsersController@addUser')
                ->name('user.add');
                // 管理员删除用户
                Route::delete('userdel/{id}', 'UsersController@destroy')
                ->name('user.destroy');

                // 管理员给指定用户添加机器
                Route::post('usermachine', 'UserMachinesController@store')
                ->name('usermachine.store');
                // 管理员修改指定用户机器
                Route::put('usermachine/{usermachine}', 'UserMachinesController@update')
                ->name('usermachine.update');
                // 管理员删除指定用户机器
                Route::delete('usermachine/{id}', 'UserMachinesController@destroy')
                ->name('usermachine.destroy');
                // 管理员查询指定用户的机器
                Route::get('usermachine/{id}', 'UserMachinesController@queryMachineMe')
                ->name('usermachine.queryMachineMe');
                // 管理员查询所有用户信息
                Route::get('usersall', 'UsersController@queryUsersAll')
                ->name('users.queryUsersAll');
                
                // 管理员添加新机器
                Route::post('machines', 'MachinesController@store')
                    ->name('machines.store');
                // 管理员修改机器信息
                Route::put('machines/{id}', 'MachinesController@update')
                ->name('machines.update');
                // 管理员删除指定机器信息
                Route::delete('machines/{id}', 'MachinesController@destroy')
                ->name('machines.destroy');
                // 管理员查询所有机器信息
                Route::get('machines/all', 'MachinesController@queryMachinesAll')
                ->name('machines.queryMachinesAll');
                
                //用户获取某设备的所有施工项目信息
                Route::get('machineallprojects/{sn}', 'ProjectsController@getAllbysn')
                ->name('machineallprojects');
                //用户获取某施工项目详情信息
                Route::get('projectdetails/{sn}/{id}', 'ProjectsController@getProjectdetails')
                ->name('projectdetails');
                
            });
        });

    Route::middleware('throttle:' . config('api.rate_limits.record'))
    ->group(function () {

        // 机器添加施工项目信息
        Route::post('projects', 'ProjectsController@store')
        ->name('projects.store');
        // 机器添加施工记录信息
        Route::post('wordrecord', 'WorkRecordsController@store')
        ->name('wordrecord.store');

        // 机器登录
        Route::post('machine_login', 'MachinesController@login')
        ->name('machine.login');

        // 添加记录信息
        Route::post('datarecord', 'DataRecordsController@store')
        ->name('datarecord.store');


    });

});
