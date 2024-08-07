<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
  
class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Basic', 
                'slug' => 'basic', 
                'stripe_plan' => 'price_1Nsk4RC7RqFU5QkALArpx8pW', 
                'price' => 10, 
                'description' => 'Basic'
            ],
            [
                'name' => 'Premium', 
                'slug' => 'premium', 
                'stripe_plan' => 'price_1NsjepC7RqFU5QkAu6M1e6t7', 
                'price' => 50, 
                'description' => 'Premium'
            ]
        ];
  
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
