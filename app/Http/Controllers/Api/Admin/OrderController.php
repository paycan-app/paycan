<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Admin Orders",
 *     description="Admin order management endpoints - view all orders system-wide"
 * )
 */
class OrderController extends AdminApiController
{
    /**
     * @OA\Get(
     *     path="/api/admin/orders",
     *     summary="List all orders (Admin)",
     *     description="Retrieve paginated and filterable list of all orders in the system (admin only)",
     *     operationId="listAllOrders",
     *     tags={"Admin Orders"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 100)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[user_id]",
     *         in="query",
     *         description="Filter by user ID",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"pending", "processing", "completed", "failed", "cancelled", "refunded"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[gateway]",
     *         in="query",
     *         description="Filter by payment gateway",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"stripe", "paypal"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[order_number]",
     *         in="query",
     *         description="Filter by order number (partial match)",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[product_id]",
     *         in="query",
     *         description="Filter by product ID",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[created_after]",
     *         in="query",
     *         description="Filter orders created after date",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[created_before]",
     *         in="query",
     *         description="Filter orders created before date",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[billing_email]",
     *         in="query",
     *         description="Filter by billing email (partial match)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="user@example.com")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="user,product,productPrice,transactions,fulfillments,subscription")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort orders (prefix with - for descending)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="-created_at", enum={"created_at", "-created_at", "total", "-total", "status", "-status", "order_number", "-order_number"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Order")
     *             ),
     *
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $query = Order::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('billing_email', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('gateway'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::scope('created_after'),
                AllowedFilter::scope('created_before'),
                'order_number',
                'billing_email',
            ])
            ->allowedIncludes([
                'user',
                'product',
                'productPrice',
                'productPrice.product',
                'transactions',
                'fulfillments',
                'subscription',
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('total'),
                AllowedSort::field('status'),
                AllowedSort::field('order_number'),
            ])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/{order}",
     *     summary="Get order details (Admin)",
     *     description="Retrieve a specific order by ID with all details (admin only)",
     *     operationId="getAdminOrder",
     *     tags={"Admin Orders"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="user,product,productPrice,transactions,fulfillments,subscription")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Order"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Order $order): JsonResponse
    {
        $order = QueryBuilder::for(Order::where('id', $order->id))
            ->allowedIncludes([
                'user',
                'product',
                'productPrice',
                'productPrice.product',
                'transactions',
                'fulfillments',
                'subscription',
            ])
            ->first();

        return response()->json(['data' => $order]);
    }
}
