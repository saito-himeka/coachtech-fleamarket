<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // モデルの読み込みを忘れずに

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 
            'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン', 
            'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName
            ]);
        }
    }
}