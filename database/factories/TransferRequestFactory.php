<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransferRequest>
 */
class TransferRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'from_store_id' => '46',
            'to_store_id' => $this->faker->randomDigit() ,
            'status' => 'open' ,
            'type' => 'in'
        ];
    }
}
