<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'user_id' => 1,
            'requester_name' => 'Rafael Rilen',
            'destination' => 'Rilenlandia',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(5)->toDateString(),
            'status' => 'solicitado',
        ]);

        Order::create([
            'user_id' => 2,
            'requester_name' => 'Admin',
            'destination' => 'Acapulco',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(5)->toDateString(),
            'status' => 'solicitado',
        ]);

    }
}
