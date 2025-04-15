<?php

namespace Tests\Feature;

use App\Models\User;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;

class OrderTest extends TestCase
{
    public function testCreateOrder()
    {
        $user = User::find(1);

        if (!$user) {
            $user = User::create([
                'name' => 'Rafa Company',
                'email' => 'rafa@teste.com',
                'password' => bcrypt('senha123'),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $mockJWTAuth = Mockery::mock('Tymon\JWTAuth\JWTAuth');
        $mockJWTAuth->shouldReceive('parseToken')->andReturnSelf();
        $mockJWTAuth->shouldReceive('authenticate')->andReturn($user);
        App::instance('Tymon\JWTAuth\JWTAuth', $mockJWTAuth);

        $startDate = now()->setTime(9, 0)->format('Y-m-d H:i:s');
        $endDate = now()->addDays(5)->setTime(18, 30)->format('Y-m-d H:i:s');

        $orderData = [
            'requester_name' => 'Rafael Rilen',
            'destination' => 'Rilenlandia',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $response = $this->postJson('/api/orders', $orderData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'requester_name' => 'Rafael Rilen',
            'destination' => 'Rilenlandia',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user_id' => $user->id,
        ]);

    }
}
