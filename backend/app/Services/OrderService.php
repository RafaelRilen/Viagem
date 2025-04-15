<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderService implements OrderServiceInterface
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {}

    public function getByFilters(Request $request): Collection
    {
        $user = $request->user();

        $orders = $this->orderRepository->getOrdersByFilters(
            isAdmin: $user->is_admin,
            userId: $user->id,
            status: $request->input('status'),
            destination: $request->input('destination'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
        );

        return $orders;
    }

    public function createOrder(OrderDTO $dto): Order
    {
        return $this->orderRepository->createOrder($dto);
    }

    public function getOrderById(int $orderId): ?Order
    {
        return $this->orderRepository->getOrderById($orderId);
    }

    public function canView(Order $order): bool
    {
        return JWTAuth::user()->id === $order->user_id;
    }

    public function updateOrderStatus(Order $order, string $status): array
    {
        $departureDate = \Carbon\Carbon::parse($order->start_date);
        $today = \Carbon\Carbon::now();

        if ($departureDate->diffInDays($today) < 2 && $status == 'cancelado') {
            return ['error' => 'Pedidos só podem ser cancelados com no mínimo 2 dias de antecedência.', 'status' => 400];
        }

        $this->orderRepository->updateOrderStatus($order, $status);

        $order->refresh();

        $order->user->notify(new OrderStatusChanged(
            status: $order->status,
            action: 'Status atualizado por administrador para ' . $status
        ));

        return ['order' => $order];
    }

}
