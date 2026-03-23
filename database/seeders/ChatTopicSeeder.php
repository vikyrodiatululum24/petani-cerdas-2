<?php

namespace Database\Seeders;

use App\Models\ChatCategory;
use App\Models\ConsutationTopics;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChatTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedData = [
            'Hama' => [
                'Daun berbintik',
                'Serangan kutu daun',
                'Ulat pada batang',
            ],
            'Penyakit' => [
                'Busuk akar',
                'Jamur pada daun',
                'Layu bakteri',
            ],
            'Nutrisi' => [
                'Kekurangan nitrogen',
                'Daun menguning',
                'Pemupukan berlebih',
            ],
        ];

        foreach ($seedData as $categoryName => $topics) {
            $category = ChatCategory::query()->firstOrCreate([
                'slug' => Str::slug($categoryName),
            ], [
                'name' => $categoryName,
            ]);

            foreach ($topics as $topicTitle) {
                ConsutationTopics::query()->firstOrCreate([
                    'slug' => Str::slug($topicTitle),
                ], [
                    'title' => $topicTitle,
                    'description' => 'Topik konsultasi seputar '.$topicTitle,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
