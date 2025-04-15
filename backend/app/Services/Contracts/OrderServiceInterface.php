<?php

namespace App\Services\Contracts;

use App\DTOs\OrderDTO;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface OrderServiceInterface
{
    public function getByFilters(Request $request): Collection;

    public function createOrder(OrderDTO $dto): Order;

    public function canView(Order $order): bool;

    public function updateOrderStatus(Order $order, string $status): array;

    public function getOrderById(int $orderId): ?Order;
}
