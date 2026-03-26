<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creatorId = User::query()
            ->where('email', 'admin@petanicerdas.id')
            ->value('id') ?? User::query()->value('id');

        if (! $creatorId) {
            return;
        }

        $testimonials = [
            [
                'name' => 'Rahmat Hidayat',
                'role' => 'Petani Padi, Klaten',
                'content' => 'Setelah ikut bimbingan di platform ini, saya jadi lebih paham pengaturan jadwal pemupukan. Hasil panen meningkat dan biaya pupuk juga lebih terkontrol.',
                'rating' => 5,
                'avatar_color' => 'bg-[#2E7D32]',
                'is_published' => true,
                'published_at' => now()->subDays(12),
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Nuraini',
                'role' => 'Petani Sayur, Magelang',
                'content' => 'Fitur konsultasi cepat sangat membantu saat tanaman saya terserang hama. Solusi yang diberikan jelas dan langsung bisa dipraktikkan di lapangan.',
                'rating' => 5,
                'avatar_color' => 'bg-blue-600',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'sort_order' => 2,
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Petani Hortikultura, Batu',
                'content' => 'Materi artikelnya ringkas dan aplikatif. Saya jadi lebih percaya diri menentukan langkah budidaya, terutama saat cuaca tidak menentu.',
                'rating' => 4,
                'avatar_color' => 'bg-orange-600',
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'sort_order' => 3,
            ],
            [
                'name' => 'Maya Lestari',
                'role' => 'Petani Cabai, Garut',
                'content' => 'Pendampingan mentor sangat responsif. Saya bisa membandingkan beberapa opsi pengendalian penyakit sebelum memutuskan tindakan terbaik.',
                'rating' => 4,
                'avatar_color' => 'bg-purple-600',
                'is_published' => true,
                'published_at' => now()->subDays(6),
                'sort_order' => 4,
            ],
            [
                'name' => 'Andi Pratama',
                'role' => 'Petani Jagung, Blitar',
                'content' => 'Secara umum sangat membantu, terutama untuk pencatatan masalah harian. Saya berharap ke depan ada lebih banyak contoh studi kasus lokal.',
                'rating' => 4,
                'avatar_color' => 'bg-rose-600',
                'is_published' => false,
                'published_at' => null,
                'sort_order' => 5,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                [
                    'name' => $testimonial['name'],
                    'content' => $testimonial['content'],
                ],
                [
                    ...$testimonial,
                    'created_by' => $creatorId,
                ],
            );
        }
    }
}
