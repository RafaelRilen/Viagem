<?php

namespace App\Interfaces;

use App\DTOs\OrderDTO;
use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function getOrdersByFilters(
        bool $isAdmin,
        int $userId,
        ?string $status = null,
        ?string $destination = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection;

    public function createOrder(OrderDTO $dto): Order;

    public function updateOrderStatus(Order $order, string $status): Order;

    public function getOrderById(int $orderId): ?Order;

}
