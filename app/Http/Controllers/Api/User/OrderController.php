<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="User Orders",
 *     description="User order management endpoints"
 * )
 */
class OrderController extends UserApiController
{
    /**
     * @OA\Get(
     *     path="/api/user/orders",
     *     summary="Get user orders",
     *     description="Retrieve paginated and filterable list of current user's orders with optional related data",
     *     operationId="getUserOrders",
     *     tags={"User Orders"},
     *     security={{"sanctum": {}}},
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
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="productPrice,productPrice.product,transactions,fulfillments")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort orders (prefix with - for descending)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="-created_at", enum={"created_at", "-created_at", "total", "-total", "status", "-status"})
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
        $orders = QueryBuilder::for(Order::where('user_id', auth()->id()))
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('gateway'),
                AllowedFilter::scope('created_after'),
                AllowedFilter::scope('created_before'),
                'order_number',
            ])
            ->allowedIncludes([
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
            ])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/user/orders/{order}",
     *     summary="Get order details",
     *     description="Retrieve a specific order by ID for the current user",
     *     operationId="getUserOrder",
     *     tags={"User Orders"},
     *     security={{"sanctum": {}}},
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
     *         @OA\Schema(type="string", example="productPrice,productPrice.product,transactions,fulfillments")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
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
     *         response=403,
     *         description="Access denied - not your order",
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
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $order = QueryBuilder::for(Order::where('id', $order->id))
            ->allowedIncludes([
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

    /**
     * @OA\Get(
     *     path="/api/user/orders/{order}/downloads",
     *     summary="Get download links",
     *     description="Get download links for digital products in an order",
     *     operationId="getOrderDownloads",
     *     tags={"User Orders"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Downloads retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(
     *                 property="downloads",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="product_title", type="string"),
     *                     @OA\Property(property="download_url", type="string"),
     *                     @OA\Property(property="expires_at", type="string"),
     *                     @OA\Property(property="downloads_remaining", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function downloads(Order $order): JsonResponse
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $order->load(['fulfillments', 'product']);

        $downloads = [];
        foreach ($order->fulfillments as $fulfillment) {
            if ($fulfillment->status !== 'completed') {
                continue;
            }

            $meta = is_array($fulfillment->meta) ? $fulfillment->meta : [];
            $downloadUrl = $fulfillment->download_url
                ?? ($meta['download_url'] ?? $meta['download_link'] ?? null);

            if (in_array($fulfillment->type, ['download', 'digital']) && $downloadUrl) {
                $downloads[] = [
                    'product_id' => $order->product->id,
                    'product_title' => $order->product->title,
                    'download_url' => $downloadUrl,
                    'expires_at' => $fulfillment->download_expires_at ?? ($meta['expires_at'] ?? null),
                    'downloads_remaining' => $fulfillment->downloads_remaining ?? ($meta['max_downloads'] ?? null),
                ];
            }
        }

        return response()->json([
            'order_id' => $order->id,
            'downloads' => $downloads,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/orders/{order}/licenses",
     *     summary="Get license keys",
     *     description="Get license keys for an order",
     *     operationId="getOrderLicenses",
     *     tags={"User Orders"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Licenses retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(
     *                 property="licenses",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="product_title", type="string"),
     *                     @OA\Property(property="license_key", type="string"),
     *                     @OA\Property(property="activated_at", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function licenses(Order $order): JsonResponse
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $order->load(['fulfillments', 'product']);

        $licenses = [];
        foreach ($order->fulfillments as $fulfillment) {
            if ($fulfillment->status !== 'completed') {
                continue;
            }

            $meta = is_array($fulfillment->meta) ? $fulfillment->meta : [];
            $licenseKey = $fulfillment->license_key ?? ($meta['license_key'] ?? null);

            if (in_array($fulfillment->type, ['license', 'digital']) && $licenseKey) {
                $licenses[] = [
                    'product_id' => $order->product->id,
                    'product_title' => $order->product->title,
                    'license_key' => $licenseKey,
                    'activated_at' => $meta['activated_at'] ?? null,
                ];
            }
        }

        return response()->json([
            'order_id' => $order->id,
            'licenses' => $licenses,
        ]);
    }
}
