<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'test',
            'email' => 'test@boardy.local',
        ]);

        $users = User::factory()->count(4)->create();
        $posts = Post::factory()->count(10)->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        Comment::factory()->count(25)->create([
            'user_id' => fn() => $users->random()->id,
            'post_id' => fn() => $posts->random()->id,
        ]);
    }
}
