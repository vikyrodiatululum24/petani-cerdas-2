<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pupuk',
                'slug' => 'pupuk',
                'description' => 'Berbagai jenis pupuk untuk kebutuhan tanaman pangan dan hortikultura.',
            ],
            [
                'name' => 'Benih',
                'slug' => 'benih',
                'description' => 'Benih unggul dengan kualitas teruji untuk hasil panen maksimal.',
            ],
            [
                'name' => 'Alat Tani',
                'slug' => 'alat-tani',
                'description' => 'Peralatan pertanian modern untuk meningkatkan efisiensi kerja.',
            ],
            [
                'name' => 'Pestisida',
                'slug' => 'pestisida',
                'description' => 'Produk pengendalian hama dan penyakit tanaman yang aman digunakan.',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }

        $products = [
            [
                'name' => 'Pupuk NPK 16-16-16 25kg',
                'slug' => 'pupuk-npk-16-16-16-25kg',
                'description' => 'Pupuk majemuk NPK seimbang untuk mendukung pertumbuhan vegetatif dan generatif tanaman.',
                'price' => 325000,
                'discount_price' => 299000,
                'category_slug' => 'pupuk',
                'olshop_links' => [
                    'shopee' => 'https://shopee.co.id/',
                    'tokopedia' => 'https://www.tokopedia.com/',
                ],
            ],
            [
                'name' => 'Benih Cabai Rawit F1 Premium',
                'slug' => 'benih-cabai-rawit-f1-premium',
                'description' => 'Benih cabai rawit F1 dengan daya tumbuh tinggi dan potensi hasil seragam.',
                'price' => 58000,
                'discount_price' => null,
                'category_slug' => 'benih',
                'olshop_links' => [
                    'shopee' => 'https://shopee.co.id/',
                    'lazada' => 'https://www.lazada.co.id/',
                ],
            ],
            [
                'name' => 'Sprayer Elektrik 16L',
                'slug' => 'sprayer-elektrik-16l',
                'description' => 'Sprayer elektrik bertekanan stabil untuk aplikasi pupuk daun dan pestisida.',
                'price' => 475000,
                'discount_price' => 449000,
                'category_slug' => 'alat-tani',
                'olshop_links' => [
                    'tokopedia' => 'https://www.tokopedia.com/',
                    'blibli' => 'https://www.blibli.com/',
                ],
            ],
            [
                'name' => 'Pestisida Nabati Konsentrat 500ml',
                'slug' => 'pestisida-nabati-konsentrat-500ml',
                'description' => 'Formulasi pestisida nabati untuk membantu pengendalian hama pada fase awal serangan.',
                'price' => 82000,
                'discount_price' => null,
                'category_slug' => 'pestisida',
                'olshop_links' => [
                    'shopee' => 'https://shopee.co.id/',
                    'tiktok_shop' => 'https://www.tiktok.com/',
                ],
            ],
            [
                'name' => 'Pupuk Organik Granul 20kg',
                'slug' => 'pupuk-organik-granul-20kg',
                'description' => 'Pupuk organik granul untuk memperbaiki struktur tanah dan meningkatkan aktivitas mikroba.',
                'price' => 165000,
                'discount_price' => 149000,
                'category_slug' => 'pupuk',
                'olshop_links' => [
                    'tokopedia' => 'https://www.tokopedia.com/',
                    'lainnya' => 'https://example.com/',
                ],
            ],
        ];

        foreach ($products as $product) {
            $categoryId = ProductCategory::query()
                ->where('slug', $product['category_slug'])
                ->value('id');

            if (! $categoryId) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'discount_price' => $product['discount_price'],
                    'category_id' => $categoryId,
                    'olshop_links' => $product['olshop_links'],
                ],
            );
        }
    }
}
