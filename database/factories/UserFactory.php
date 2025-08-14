<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'account' => $this->faker->unique()->safeEmail(),
            'email' => function (array $attributes) {
                return $attributes['account'];
            },
            'password' => Hash::make('password'),
            'prvilige' => 'B',
            'status' => 'active',
            'phone' => $this->faker->phoneNumber(),
            'veri_code' => strval(rand(100000, 999999)),
            'veri_expire' => now()->addMinutes(7),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'prvilige' => 'A',
            ];
        });
    }
}
