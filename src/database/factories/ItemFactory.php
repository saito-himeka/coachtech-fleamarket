<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'condition_id' => \App\Models\Condition::factory(), // Conditionも自動生成
            'name' => $this->faker->word() . 'の商品',
            'brand' => 'サンプルブランド',
            'price' => 1000,
            'description' => 'これはテスト用の商品説明です。',
            'image_paths' => ['test_image.jpg'],// JSON形式で保存
        ];
    }
}