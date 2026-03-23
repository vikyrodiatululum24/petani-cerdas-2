<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Budidaya',
                'slug' => 'budidaya',
                'description' => 'Tips dan teknik budidaya tanaman untuk hasil panen optimal.',
            ],
            [
                'name' => 'Pengendalian Hama',
                'slug' => 'pengendalian-hama',
                'description' => 'Panduan mengatasi hama dan penyakit tanaman secara efektif.',
            ],
            [
                'name' => 'Pascapanen',
                'slug' => 'pascapanen',
                'description' => 'Manajemen pascapanen untuk menjaga kualitas dan nilai jual.',
            ],
            [
                'name' => 'Teknologi Pertanian',
                'slug' => 'teknologi-pertanian',
                'description' => 'Inovasi alat dan teknologi modern untuk sektor pertanian.',
            ],
        ];

        foreach ($categories as $category) {
            ArticleCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }

        $authorId = User::query()->value('id');

        if (! $authorId) {
            return;
        }

        $articles = [
            [
                'title' => 'Cara Menyusun Jadwal Pemupukan Padi Musim Hujan',
                'slug' => 'cara-menyusun-jadwal-pemupukan-padi-musim-hujan',
                'content' => 'Pemupukan pada musim hujan perlu disesuaikan agar unsur hara tidak mudah tercuci. Fokus utama adalah waktu aplikasi, dosis bertahap, dan pemantauan kondisi lahan secara berkala.',
                'is_published' => true,
                'category_slug' => 'budidaya',
            ],
            [
                'title' => 'Identifikasi Dini Ulat Grayak pada Jagung',
                'slug' => 'identifikasi-dini-ulat-grayak-pada-jagung',
                'content' => 'Gejala serangan ulat grayak dapat dideteksi dari kerusakan daun muda. Pengamatan rutin di pagi dan sore hari membantu petani mengambil tindakan cepat sebelum serangan meluas.',
                'is_published' => true,
                'category_slug' => 'pengendalian-hama',
            ],
            [
                'title' => 'Standar Sortasi Cabai Merah untuk Pasar Modern',
                'slug' => 'standar-sortasi-cabai-merah-untuk-pasar-modern',
                'content' => 'Sortasi yang baik meningkatkan harga jual dan menekan komplain pembeli. Artikel ini membahas indikator kualitas, ukuran seragam, dan teknik pengemasan sederhana.',
                'is_published' => false,
                'category_slug' => 'pascapanen',
            ],
            [
                'title' => 'Memanfaatkan Sensor Kelembapan Tanah untuk Irigasi Presisi',
                'slug' => 'memanfaatkan-sensor-kelembapan-tanah-untuk-irigasi-presisi',
                'content' => 'Sensor kelembapan membantu menentukan kebutuhan air secara akurat. Dengan irigasi presisi, penggunaan air lebih efisien dan pertumbuhan tanaman tetap optimal.',
                'is_published' => true,
                'category_slug' => 'teknologi-pertanian',
            ],
            [
                'title' => 'Checklist Persiapan Lahan Sebelum Tanam Sayuran',
                'slug' => 'checklist-persiapan-lahan-sebelum-tanam-sayuran',
                'content' => 'Persiapan lahan meliputi analisis pH tanah, pengolahan bedengan, dan manajemen drainase. Checklist ini membantu proses tanam lebih rapi dan terukur.',
                'is_published' => false,
                'category_slug' => 'budidaya',
            ],
        ];

        foreach ($articles as $article) {
            $categoryId = ArticleCategory::query()
                ->where('slug', $article['category_slug'])
                ->value('id');

            if (! $categoryId) {
                continue;
            }

            Article::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'is_published' => $article['is_published'],
                    'author_id' => $authorId,
                    'category_id' => $categoryId,
                ],
            );
        }
    }
}
