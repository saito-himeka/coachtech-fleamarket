<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category; // 追加
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. テスト用ユーザーの作成
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. コンディションの作成
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];
        $conditionIds = [];
        foreach ($conditions as $name) {
            $c = Condition::create(['name' => $name]);
            $conditionIds[$name] = $c->id;
        }

        // 3. カテゴリの作成
        // 主要なカテゴリをあらかじめ作成しておきます
        $categoryNames = ['ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 'コスメ', 'キッチン', 'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[$name] = Category::create(['name' => $name]);
        }

        // 4. 商品データの作成
        $itemsData = [
            [
                'name' => '腕時計', 'price' => 15000, 'brand' => 'Rolax', 'condition' => '良好',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'category_list' => ['ファッション', 'メンズ', 'アクセサリー']
            ],
            [
                'name' => 'HDD', 'price' => 5000, 'brand' => '西芝', 'condition' => '目立った傷や汚れなし',
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'category_list' => ['家電']
            ],
            [
                'name' => '玉ねぎ3束', 'price' => 300, 'brand' => 'なし', 'condition' => 'やや傷や汚れあり',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'category_list' => ['キッチン']
            ],
            [
                'name' => '革靴', 'price' => 4000, 'brand' => null, 'condition' => '状態が悪い',
                'description' => 'クラシックなデザインの革靴',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'category_list' => ['ファッション', 'メンズ']
            ],
            [
                'name' => 'ノートPC', 'price' => 45000, 'brand' => null, 'condition' => '良好',
                'description' => '高性能なノートパソコン',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'category_list' => ['家電']
            ],
            [
                'name' => 'マイク', 'price' => 8000, 'brand' => 'なし', 'condition' => '目立った傷や汚れなし',
                'description' => '高音質のレコーディング用マイク',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'category_list' => ['家電']
            ],
            [
                'name' => 'ショルダーバッグ', 'price' => 3500, 'brand' => null, 'condition' => 'やや傷や汚れあり',
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'category_list' => ['ファッション', 'レディース']
            ],
            [
                'name' => 'タンブラー', 'price' => 500, 'brand' => 'なし', 'condition' => '状態が悪い',
                'description' => '使いやすいタンブラー',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'category_list' => ['キッチン', 'インテリア']
            ],
            [
                'name' => 'コーヒーミル', 'price' => 4000, 'brand' => 'Starbacks', 'condition' => '良好',
                'description' => '手動のコーヒーミル',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'category_list' => ['キッチン', '家電']
            ],
            [
                'name' => 'メイクセット', 'price' => 2500, 'brand' => null, 'condition' => '目立った傷や汚れなし',
                'description' => '便利なメイクアップセット',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'category_list' => ['コスメ']
            ],
        ];

        foreach ($itemsData as $data) {
            $item = Item::create([
                'user_id' => $user->id,
                'condition_id' => $conditionIds[$data['condition']],
                'name' => $data['name'],
                'price' => $data['price'],
                'brand' => $data['brand'],
                'description' => $data['description'],
                'image_paths' => [$data['image_url']],
            ]);

            // カテゴリの紐付け（中間テーブルへの挿入）
            foreach ($data['category_list'] as $catName) {
                if (isset($categories[$catName])) {
                    $item->categories()->attach($categories[$catName]->id);
                }
            }
        }
    }
}