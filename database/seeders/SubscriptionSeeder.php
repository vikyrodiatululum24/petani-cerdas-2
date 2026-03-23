<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petaniUser = User::where('email', 'petani@petanicerdas.id')->first();
        $adminUser = User::where('email', 'admin@petanicerdas.id')->first();

        if (! $petaniUser || ! $adminUser) {
            return;
        }

        $basicPlan = SubscriptionPlan::where('name', 'Basic')->first();
        $premiumPlan = SubscriptionPlan::where('name', 'Premium')->first();
        $elitePlan = SubscriptionPlan::where('name', 'Elite')->first();

        if (! $basicPlan || ! $premiumPlan || ! $elitePlan) {
            return;
        }

        // Create active subscription (Premium)
        Subscription::updateOrCreate(
            [
                'user_id' => $petaniUser->id,
                'subscription_plan_id' => $premiumPlan->id,
                'generation' => 1,
            ],
            [
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addMonths(1),
                'status' => 'active',
                'approved_by' => $adminUser->id,
            ]
        );

        // Create inactive subscription (Basic)
        Subscription::updateOrCreate(
            [
                'user_id' => $petaniUser->id,
                'subscription_plan_id' => $basicPlan->id,
                'generation' => 1,
            ],
            [
                'start_date' => Carbon::now()->subMonths(3)->startOfMonth(),
                'end_date' => Carbon::now()->subMonths(2)->startOfMonth(),
                'status' => 'inactive',
                'approved_by' => $adminUser->id,
            ]
        );

        // Create pending subscription (Elite)
        Subscription::updateOrCreate(
            [
                'user_id' => $petaniUser->id,
                'subscription_plan_id' => $elitePlan->id,
                'generation' => 1,
            ],
            [
                'start_date' => null,
                'end_date' => null,
                'status' => 'pending',
                'approved_by' => null,
            ]
        );
    }
}
