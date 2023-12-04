<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => null,
            'user_id' => null,
            'review' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1,5),
            'created_at' => fake()->dateTimebetween('-2 years'),
            'updated_at' => fake()->dateTimebetween('created_at', 'now')
        ];
    }

    public function goodReviews(){
        return $this->state(function(array $attributes){
            return [
                'rating' => fake()->numberBetween(4,5)
            ];
        });
    }

    public function averageReviews(){
        return $this->state(function(array $attributes){
            return [
                'rating' => fake()->numberBetween(2,3)
            ];
        });
    }

    public function badReviews(){
        return $this->state(function(array $attributes){
            return [
                'rating' => fake()->numberBetween(1,2)
            ];
        });
    }
}
