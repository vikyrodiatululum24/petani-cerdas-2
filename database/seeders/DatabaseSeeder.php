<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\ChatTopicSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UsersSeeder::class,
            ArticleSeeder::class,
            ProductSeeder::class,
            SubscriptionPlanSeeder::class,
            SubscriptionSeeder::class,
            ChatTopicSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
