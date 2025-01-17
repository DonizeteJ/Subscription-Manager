<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::create([
            'user_id' => User::first()->id,
            'name' => 'Subscription 1',
            'description' => 'Subscription 1',
            'price' => 50
        ]);

        $this->command->info('Subscription plan created successfully.');
    }
}
