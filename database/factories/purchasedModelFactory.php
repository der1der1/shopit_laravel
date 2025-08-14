<?php

namespace Database\Factories;

use App\Models\purchasedModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class purchasedModelFactory extends Factory
{
    protected $model = purchasedModel::class;

    public function definition()
    {
        return [
            'account' => $this->faker->email,
            'purchased' => '1,2',
            'name' => $this->faker->name,
            'to_shop' => $this->faker->address,
            'to_address' => $this->faker->address,
            'shop1_addr2' => '1',
            'bank_account' => $this->faker->bankAccountNumber,
            'show' => '0',
            'payed' => '0',
            'delivered' => '0',
            'bill' => $this->faker->numberBetween(1000, 9999),
        ];
    }
}