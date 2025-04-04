<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function PHPSTORM_META\type;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['Individual','Business']);
        $name = $type == 'Individual'? $this->faker->name() : $this->faker->company();

        return [
            'type' => $type,
            'name' => $name,
            'email' => $this->faker->email(),
            'address' => $this->faker->streetAddress(),
            'state' => $this->faker->state(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
        ];
    }
}
