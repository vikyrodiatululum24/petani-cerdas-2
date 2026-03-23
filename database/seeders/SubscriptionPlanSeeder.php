<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptionPlans = [
            [
                'name' => 'Basic',
                'price' => 200000,
                'discount_price' => 145000,
                'quota' => 30,
                'features' => ['Free guide book dasar pertanian', 'Konsultasi live chat edukatif 2x24 jam by antrian bersama petani (Historycal Live Chat)', 'Akses daily artikel dan informasi pertanian'],
                'description' => 'Paket untuk petani yang baru memulai.',
                'duration_months' => 1,
                'is_featured' => false,
            ],
            [
                'name' => 'Premium',
                'price' => 250000,
                'discount_price' => 199000,
                'quota' => 20,
                'features' => ['Free guide book dasar pertanian', 'Konsultasi live chat edukatif 2x24 jam by antrian bersama petani (Historycal Live Chat)', 'Akses daily artikel dan informasi pertanian', 'Priority konsultasi (fast responship)', 'Akses daily artikel dan informasi pertanian', 'Live session Klinik Tanam Petani Cerdas (2x / bulan)', 'Strategi musim tanam'],
                'description' => 'Paket untuk petani yang ingin lebih banyak akses dan dukungan.',
                'duration_months' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Elite',
                'price' => 1399000,
                'discount_price' => 799000,
                'quota' => 5,
                'features' => ['Semua fitur paket Premium + fast direct konsultasi', 'Weekly privat live session', 'Profilling kolaborasi antar petani', 'Analisa bisnis personal', 'Highlight promosi di platform Petani Cerdas'],
                'description' => 'Paket eksklusif untuk petani yang ingin pendampingan intensif dan personal.',
                'duration_months' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($subscriptionPlans as $plan) {
            \App\Models\SubscriptionPlan::create($plan);
        }
    }
}
