<?php

namespace Tests\Feature;

use App\Models\User;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;

class OrderUpdateTest extends TestCase
{
    public function testUpdateStatusFail()
    {
        $user = User::find(1);

        if (!$user) {
            $user = User::create([
                'name' => 'Rafa Company',
                'email' => 'rafa@teste.com',
                'password' => bcrypt('senha123'),
                'is_admin' => 0
            ]);
        }

        $order = $user->orders()->create([
            'requester_name' => 'Rafael Rilen',
            'destination' => 'Rilenlandia',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'solicitado',
        ]);

        $token = JWTAuth::fromUser($user);

        $mockJWTAuth = Mockery::mock('Tymon\JWTAuth\JWTAuth');
        $mockJWTAuth->shouldReceive('parseToken')->andReturnSelf();
        $mockJWTAuth->shouldReceive('authenticate')->andReturn($user);
        App::instance('Tymon\JWTAuth\JWTAuth', $mockJWTAuth);

        $updateData = [
            'status' => 'em andamento'
        ];

        $response = $this->patchJson("/api/orders/{$order->id}/status", $updateData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Apenas administradores podem alterar o status do pedido.'
        ]);
    }

}
