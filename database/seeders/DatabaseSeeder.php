<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::factory(3)->create()->each(function ($book){
            $user = User::factory()->create();
            $numReviews = random_int(5,30); 
            Review::factory()->count($numReviews)->goodReviews()->for($book)->create([
                'user_id' => $user->id
            ]);
        });

        Book::factory(3)->create()->each(function ($book){
            $user = User::factory()->create();
            $numReviews = random_int(5,30); 
            Review::factory()->count($numReviews)->averageReviews()->for($book)->create([
                'user_id' => $user->id
            ]);
        });

        Book::factory(4)->create()->each(function ($book){
            $user = User::factory()->create();
            $numReviews = random_int(5,30); 
            Review::factory()->count($numReviews)->badReviews()->for($book)->create([
                'user_id' => $user->id
            ]);
        });
    }
}
