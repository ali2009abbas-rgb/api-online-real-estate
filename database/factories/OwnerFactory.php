<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OwnerFactory extends Factory
{
    protected $model = \App\Models\Owner::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => '+963' . rand(910000000, 999999999),
            'internal_notes' => 'مستعجل على البيع وبقبول عرض منافس',
        ];
    }
}
