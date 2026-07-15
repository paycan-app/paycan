<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Admin Transactions",
 *     description="Admin transaction management endpoints"
 * )
 */
class TransactionController extends AdminApiController
{
    /**
     * @OA\Get(
     *     path="/api/admin/transactions",
     *     summary="List all transactions (Admin)",
     *     description="Retrieve paginated and filterable list of all transactions (admin only)",
     *     operationId="listAllTransactions",
     *     tags={"Admin Transactions"},
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
     *         name="filter[status]",
     *         in="query",
     *         description="Filter by transaction status",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"pending", "completed", "failed", "refunded"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[type]",
     *         in="query",
     *         description="Filter by transaction type",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"payment", "refund", "subscription"})
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
     *         name="filter[user_id]",
     *         in="query",
     *         description="Filter by user ID",
     *         required=false,
     *
     *         @OA\Schema(type="string", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="user,order,subscription")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort transactions (prefix with - for descending)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="-created_at", enum={"created_at", "-created_at", "amount", "-amount"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Transactions retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(type="object")
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
        $query = Transaction::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('gateway_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('gateway'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::callback('amount_min', fn ($q, $value) => $q->where('amount', '>=', $value)),
                AllowedFilter::callback('amount_max', fn ($q, $value) => $q->where('amount', '<=', $value)),
            ])
            ->allowedIncludes([
                'user',
                'order',
                'order.productPrice',
                'order.productPrice.product',
                'subscription',
                'subscription.productPrice',
                'subscription.productPrice.product',
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('amount'),
            ])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($transactions);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/transactions/{transaction}",
     *     summary="Get transaction details (Admin)",
     *     description="Retrieve a specific transaction by ID (admin only)",
     *     operationId="getTransaction",
     *     tags={"Admin Transactions"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="transaction",
     *         in="path",
     *         description="Transaction ID",
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
     *         @OA\Schema(type="string", example="user,order,subscription")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Transaction retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object"
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
     *         description="Transaction not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction = QueryBuilder::for(Transaction::where('id', $transaction->id))
            ->allowedIncludes([
                'user',
                'order',
                'order.productPrice',
                'order.productPrice.product',
                'subscription',
                'subscription.productPrice',
                'subscription.productPrice.product',
            ])
            ->first();

        return response()->json(['data' => $transaction]);
    }
}
