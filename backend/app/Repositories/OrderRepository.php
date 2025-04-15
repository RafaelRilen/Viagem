<?php
namespace App\Repositories;

use App\DTOs\OrderDTO;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderRepository implements OrderRepositoryInterface
{

    public function getOrderById(int $orderId): ?Order
    {
        return Order::find($orderId);
    }

    public function getOrdersByFilters(
        bool $isAdmin,
        int $userId,
        ?string $status = null,
        ?string $destination = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {

        $query = $isAdmin
            ? Order::on('mysql')
            : Order::on('mysql')->where('user_id', $userId);

        $query->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($destination) {
            $query->where('destino', 'like', '%' . $destination . '%');
        }

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('data_de_ida', [$startDate, $endDate])
                    ->orWhereBetween('data_de_volta', [$startDate, $endDate]);
            });
        }

        return $query->get();
    }

    public function createOrder(OrderDTO $dto): Order
    {
        $user =  $user = JWTAuth::user();
        return $user->orders()->create([
            ...$dto->toArray(),
            'status' => 'solicitado',
        ]);
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order;
    }



}
