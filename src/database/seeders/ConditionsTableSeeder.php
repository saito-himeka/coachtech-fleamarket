<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    public function run()
    {
        // 追加：実行前に中身を一度空にする
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // 制約を一時無効化
        DB::table('conditions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // 制約を戻す

        DB::table('conditions')->insert([
            ['name' => '良好'],
            ['name' => '目立った傷や汚れなし'],
            ['name' => 'やや傷や汚れあり'],
            ['name' => '状態が悪い'],
        ]);
    }
}