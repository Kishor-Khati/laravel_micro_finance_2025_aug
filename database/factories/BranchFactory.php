<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Branch',
            'code' => fake()->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'address' => fake()->address(),
            'phone' => '01-' . fake()->numberBetween(4000000, 4999999),
            'email' => fake()->unique()->companyEmail(),
            'manager_name' => fake()->name(),
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}