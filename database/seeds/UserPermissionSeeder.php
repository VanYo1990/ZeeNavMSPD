<?php

use Illuminate\Database\Seeder;

use App\Models\User;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // //
        // // 初始化用户角色，将 1 号用户指派为『站长』
        // $user = User::find(1);
        // $user->assignRole('Founder');

        // 将 2 号用户指派为『管理员』
        $user = User::find(4);
        $user->assignRole('Maintainer');
    }
}
