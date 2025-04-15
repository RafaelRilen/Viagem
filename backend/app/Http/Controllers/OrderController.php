<?php

namespace App\Http\Controllers;

use App\DTOs\OrderDTO;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    public function __construct(
        private OrderServiceInterface $orderService

    ) {}

    public function index(Request $request): OrderCollection
    {
        $orders = $this->orderService->getByFilters($request);
        return new OrderCollection($orders);
    }

    public function store(OrderDTO $dto): OrderResource
    {
        $order = $this->orderService->createOrder($dto);
        return new OrderResource($order);
    }

    public function show($orderId): OrderResource|JsonResponse
    {
        $orderId = (int) $orderId;

        $order = $this->orderService->getOrderById($orderId);

        if (!$order) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        if (!$this->orderService->canView($order)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new OrderResource($order);
    }

    public function updateStatus(Request $request, Order $order): OrderResource|JsonResponse
    {
        $user = JWTAuth::user();

        if (!$user->is_admin) {
            return response()->json([
                'message' =>  'Apenas administradores podem alterar o status do pedido.'
            ], 403);
        }

        if (!$request->has('status')) {
            return response()->json([
                'message' => 'Status não encontrado para atualizar o pedido.',
            ], 422);
        }

        $status = $request->status;

        if ($user->id === $order->user_id) {
            return response()->json([
                'message' => 'Você não pode alterar o status do seu próprio pedido'
            ], 403);
        }

        $validStatuses = ['solicitado', 'aprovado', 'cancelado'];
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' =>'Status inválido.'
            ], 403);
        }

        if ($order->status == 'cancelado' && $status == 'cancelado') {
            return response()->json([
                'message' =>'O pedido já está cancelado.'
            ], 403);
        }

        $result = $this->orderService->updateOrderStatus($order, $status);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return new OrderResource($result['order']);
    }

}
