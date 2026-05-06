<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * 创建管理员账号
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@signify.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->command->info('管理员账号已创建: admin@signify.com / password');
        $this->command->warn('⚠️  请立即修改默认密码，此 Seeder 仅用于开发/测试环境！');
    }
}
