<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\InsufficientCreditsException;
use App\Http\Requests\Api\Admin\DeductCreditsRequest;
use App\Http\Requests\Api\Admin\TopupCreditsRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Admin Wallets",
 *     description="Admin endpoints for managing user credit wallets and atomic usage deductions"
 * )
 */
class WalletController extends AdminApiController
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/admin/users/{user}/wallets",
     *     summary="Get user wallets (Admin)",
     *     description="Retrieve all credit wallets for a specific user",
     *     operationId="adminGetUserWallets",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User wallets retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Wallet"))
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="User not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function index(string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $wallets = $this->walletService->getWalletsForUser($user);

        return response()->json([
            'success' => true,
            'data' => $wallets,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{user}/wallets/{type}/transactions",
     *     summary="Get wallet transaction ledger for specific wallet (Admin)",
     *     description="Retrieve paginated transaction history for a user's specific wallet type",
     *     operationId="adminGetWalletTransactions",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Wallet type (e.g. basic, premium)",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20, maximum=100)),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, description="Filter direction (credit, debit)", @OA\Schema(type="string", enum={"credit", "debit"})),
     *     @OA\Parameter(name="filter[action]", in="query", required=false, description="Filter action", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[reference_id]", in="query", required=false, description="Filter reference ID", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Wallet transactions retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WalletTransaction")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="User not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function transactions(string $userId, string $type): JsonResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $wallet = $this->walletService->getOrCreateWallet($user, $type);

        $perPage = min((int) request()->get('per_page', 20), 100);

        $query = WalletTransaction::where('wallet_id', $wallet->id);

        if (! request()->has('filter.action') && ($action = request()->query('action'))) {
            $query->where('action', $action);
        }
        if (! request()->has('filter.type') && ($direction = request()->query('type'))) {
            $query->where('type', $direction);
        }
        if (! request()->has('filter.reference_id') && ($refId = request()->query('reference_id'))) {
            $query->where('reference_id', $refId);
        }

        $transactions = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('action'),
                AllowedFilter::exact('reference_id'),
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage);

        return response()->json($transactions);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{user}/wallets/transactions",
     *     summary="Get all customer wallet transactions (Admin)",
     *     description="Retrieve paginated transaction history across all wallets for a specific user",
     *     operationId="adminGetUserAllWalletTransactions",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20, maximum=100)),
     *     @OA\Parameter(name="filter[wallet_type]", in="query", required=false, description="Filter by wallet type (basic, premium)", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, description="Filter direction (credit, debit)", @OA\Schema(type="string", enum={"credit", "debit"})),
     *     @OA\Parameter(name="filter[action]", in="query", required=false, description="Filter action", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[reference_id]", in="query", required=false, description="Filter reference ID", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Transactions retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WalletTransaction")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="User not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function allTransactions(string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $perPage = min((int) request()->get('per_page', 20), 100);

        $query = WalletTransaction::where('user_id', $user->id)->with('wallet');

        if (! request()->has('filter.wallet_type') && ($walletType = request()->query('wallet_type'))) {
            $query->whereHas('wallet', fn ($q) => $q->where('type', $walletType));
        }
        if (! request()->has('filter.action') && ($action = request()->query('action'))) {
            $query->where('action', $action);
        }
        if (! request()->has('filter.type') && ($type = request()->query('type'))) {
            $query->where('type', $type);
        }
        if (! request()->has('filter.reference_id') && ($refId = request()->query('reference_id'))) {
            $query->where('reference_id', $refId);
        }

        $transactions = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('action'),
                AllowedFilter::exact('reference_id'),
                AllowedFilter::callback('wallet_type', fn ($query, $value) => $query->whereHas('wallet', fn ($q) => $q->where('type', $value))),
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage);

        return response()->json($transactions);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{user}/wallets/usages",
     *     summary="Get customer usage logs (Admin)",
     *     description="Retrieve paginated credit usage logs (AI agent executions, prompt usage) for a specific customer",
     *     operationId="adminGetUserWalletUsages",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20, maximum=100)),
     *     @OA\Parameter(name="filter[wallet_type]", in="query", required=false, description="Filter by wallet type (basic, premium)", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[reference_id]", in="query", required=false, description="Filter task or run reference ID", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Usage logs retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WalletTransaction")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="User not found", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function usages(string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $perPage = min((int) request()->get('per_page', 20), 100);

        $query = WalletTransaction::where('user_id', $user->id)->where('action', 'usage')->with('wallet');

        if (! request()->has('filter.wallet_type') && ($walletType = request()->query('wallet_type'))) {
            $query->whereHas('wallet', fn ($q) => $q->where('type', $walletType));
        }
        if (! request()->has('filter.reference_id') && ($refId = request()->query('reference_id'))) {
            $query->where('reference_id', $refId);
        }

        $usages = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('reference_id'),
                AllowedFilter::callback('wallet_type', fn ($query, $value) => $query->whereHas('wallet', fn ($q) => $q->where('type', $value))),
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage);

        return response()->json($usages);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/wallets/transactions",
     *     summary="List all customer wallet transactions globally (Admin)",
     *     description="Retrieve all wallet transactions across all customers with filtering",
     *     operationId="adminListGlobalWalletTransactions",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20, maximum=100)),
     *     @OA\Parameter(name="filter[user_id]", in="query", required=false, description="Filter by user ID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[wallet_type]", in="query", required=false, description="Filter by wallet type", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, description="Filter direction (credit, debit)", @OA\Schema(type="string", enum={"credit", "debit"})),
     *     @OA\Parameter(name="filter[action]", in="query", required=false, description="Filter action", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[reference_id]", in="query", required=false, description="Filter reference ID", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Transactions retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WalletTransaction")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     )
     * )
     */
    public function globalTransactions(): JsonResponse
    {
        $perPage = min((int) request()->get('per_page', 20), 100);

        $query = WalletTransaction::query()->with(['wallet', 'user']);

        if (! request()->has('filter.user_id') && ($filterUserId = request()->query('user_id'))) {
            $query->where('user_id', $filterUserId);
        }
        if (! request()->has('filter.wallet_type') && ($walletType = request()->query('wallet_type'))) {
            $query->whereHas('wallet', fn ($q) => $q->where('type', $walletType));
        }
        if (! request()->has('filter.action') && ($action = request()->query('action'))) {
            $query->where('action', $action);
        }
        if (! request()->has('filter.type') && ($type = request()->query('type'))) {
            $query->where('type', $type);
        }
        if (! request()->has('filter.reference_id') && ($refId = request()->query('reference_id'))) {
            $query->where('reference_id', $refId);
        }

        $transactions = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('action'),
                AllowedFilter::exact('reference_id'),
                AllowedFilter::callback('wallet_type', fn ($query, $value) => $query->whereHas('wallet', fn ($q) => $q->where('type', $value))),
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage);

        return response()->json($transactions);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/wallets/usages",
     *     summary="List all customer credit usage logs globally (Admin)",
     *     description="Retrieve all credit usage logs across all customers",
     *     operationId="adminListGlobalWalletUsages",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20, maximum=100)),
     *     @OA\Parameter(name="filter[user_id]", in="query", required=false, description="Filter by user ID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[wallet_type]", in="query", required=false, description="Filter by wallet type", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[reference_id]", in="query", required=false, description="Filter task or run reference ID", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Usages retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WalletTransaction")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     )
     * )
     */
    public function globalUsages(): JsonResponse
    {
        $perPage = min((int) request()->get('per_page', 20), 100);

        $query = WalletTransaction::where('action', 'usage')->with(['wallet', 'user']);

        if (! request()->has('filter.user_id') && ($filterUserId = request()->query('user_id'))) {
            $query->where('user_id', $filterUserId);
        }
        if (! request()->has('filter.wallet_type') && ($walletType = request()->query('wallet_type'))) {
            $query->whereHas('wallet', fn ($q) => $q->where('type', $walletType));
        }
        if (! request()->has('filter.reference_id') && ($refId = request()->query('reference_id'))) {
            $query->where('reference_id', $refId);
        }

        $usages = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('reference_id'),
                AllowedFilter::callback('wallet_type', fn ($query, $value) => $query->whereHas('wallet', fn ($q) => $q->where('type', $value))),
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage);

        return response()->json($usages);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/wallets/deduct",
     *     summary="Deduct credits for usage (Admin / AI Agent)",
     *     description="Atomically deduct credits from a user's wallet with row locking. Designed for AI agent runtimes.",
     *     operationId="adminDeductCredits",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_id", "amount"},
     *
     *             @OA\Property(property="user_id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6"),
     *             @OA\Property(property="wallet_type", type="string", default="basic", example="premium"),
     *             @OA\Property(property="amount", type="number", format="float", example=2.5),
     *             @OA\Property(property="reference_id", type="string", example="agent_run_492"),
     *             @OA\Property(property="description", type="string", example="Agent run - GPT-4o"),
     *             @OA\Property(property="meta", type="object", example={"model": "gpt-4o", "tokens": 1500})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Credits deducted successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/DeductCreditsResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Insufficient credits or validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/InsufficientCreditsErrorResponse")
     *     )
     * )
     */
    public function deduct(DeductCreditsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::findOrFail($validated['user_id']);
        $walletType = $validated['wallet_type'] ?? 'basic';
        $amount = (float) $validated['amount'];
        $referenceId = $validated['reference_id'] ?? null;
        $description = $validated['description'] ?? null;
        $meta = $validated['meta'] ?? [];

        try {
            $transaction = $this->walletService->deductCredits(
                target: $user,
                amount: $amount,
                type: $walletType,
                description: $description,
                referenceId: $referenceId,
                meta: $meta
            );

            return response()->json([
                'success' => true,
                'message' => 'Credits deducted successfully',
                'data' => [
                    'transaction' => $transaction,
                    'wallet' => [
                        'type' => $walletType,
                        'balance' => (float) $transaction->balance_after,
                    ],
                ],
            ]);
        } catch (InsufficientCreditsException $e) {
            return response()->json([
                'success' => false,
                'error' => 'insufficient_credits',
                'message' => $e->getMessage(),
                'data' => [
                    'wallet_type' => $e->walletType,
                    'current_balance' => (float) $e->currentBalance,
                    'required_amount' => (float) $e->requiredAmount,
                ],
            ], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/wallets/topup",
     *     summary="Top up credits (Admin)",
     *     description="Add credits to a user's wallet manually or programmatically",
     *     operationId="adminTopupCredits",
     *     tags={"Admin Wallets"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_id", "amount"},
     *
     *             @OA\Property(property="user_id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6"),
     *             @OA\Property(property="wallet_type", type="string", default="basic", example="basic"),
     *             @OA\Property(property="amount", type="number", format="float", example=500),
     *             @OA\Property(property="action", type="string", default="manual_adjustment", example="manual_adjustment"),
     *             @OA\Property(property="description", type="string", example="Promotional credit top-up"),
     *             @OA\Property(property="reference_id", type="string", example="promo_march"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Credits added successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Credits added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="transaction", ref="#/components/schemas/WalletTransaction"),
     *                 @OA\Property(
     *                     property="wallet",
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="basic"),
     *                     @OA\Property(property="balance", type="number", format="float", example=500)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function topup(TopupCreditsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::findOrFail($validated['user_id']);
        $walletType = $validated['wallet_type'] ?? 'basic';
        $amount = (float) $validated['amount'];
        $action = $validated['action'] ?? 'manual_adjustment';
        $referenceId = $validated['reference_id'] ?? null;
        $description = $validated['description'] ?? null;
        $meta = $validated['meta'] ?? [];

        $transaction = $this->walletService->addCredits(
            target: $user,
            amount: $amount,
            type: $walletType,
            action: $action,
            description: $description,
            referenceId: $referenceId,
            meta: $meta
        );

        return response()->json([
            'success' => true,
            'message' => 'Credits added successfully',
            'data' => [
                'transaction' => $transaction,
                'wallet' => [
                    'type' => $walletType,
                    'balance' => (float) $transaction->balance_after,
                ],
            ],
        ]);
    }
}
