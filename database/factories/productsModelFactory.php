<?php

namespace Database\Factories;

use App\Models\productsModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class productsModelFactory extends Factory
{
    protected $model = productsModel::class;

    public function definition()
    {
        return [
            'product_name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['電腦', '手機', '相機']),
            'intro' => $this->faker->paragraph,
            'price' => $this->faker->numberBetween(100, 10000),
            'storage' => $this->faker->numberBetween(1, 100),
            'img' => 'default.jpg',
            'show' => 1,
            'hot' => $this->faker->boolean(20),
        ];
    }
}