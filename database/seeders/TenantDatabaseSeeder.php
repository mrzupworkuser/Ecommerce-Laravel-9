<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::create([
            'user_id' => 1,
            'title' => 'Welcome',
            'body' => "Try creating another blog post here, then register as another tenant on your central domain. You'll see the data separation in practice.",
        ]);

        Post::create([
            'user_id' => 1,
            'title' => 'README!',
            'body' => "Be sure to check the README.md file. It explains how things are structured, why they're structured that way and how to make the most out of this boilerplate.",
        ]);

        Post::create([
            'user_id' => 1,
            'title' => '🚀 Ship fast',
            'body' => "As always, don't forget to ship fast 😎. We hope this boilerplate saves you a lot of development time and lets you get to production much faster.",
        ]);
    }
}
