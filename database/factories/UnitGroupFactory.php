<?php

namespace Database\Factories;

use App\Models\Unit_group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UnitGroupFactory extends Factory
{
    protected $model = Unit_group::class;

    public function definition(): array
    {
        return [
            'unit_group_code' => $this->faker->word(),
            'unit_group_name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
