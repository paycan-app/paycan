<?php

namespace App\Http\Controllers\Api\User;

use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="User Wallets",
 *     description="User wallet and credit management endpoints"
 * )
 */
class WalletController extends UserApiController
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/user/wallets",
     *     summary="Get user wallets",
     *     description="Retrieve all credit wallets and current balances for the authenticated user",
     *     operationId="getUserWallets",
     *     tags={"User Wallets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Wallets retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Wallet"))
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $wallets = $this->walletService->getWalletsForUser($user);

        return response()->json([
            'success' => true,
            'data' => $wallets,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/wallets/{type}",
     *     summary="Get specific user wallet",
     *     description="Retrieve details and current balance for a specific wallet type (e.g. basic, premium)",
     *     operationId="getUserWalletByType",
     *     tags={"User Wallets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Wallet type (e.g. basic, premium)",
     *
     *         @OA\Schema(type="string", example="premium")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Wallet retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Wallet")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function show(string $type): JsonResponse
    {
        $user = auth()->user();
        $wallet = $this->walletService->getOrCreateWallet($user, $type);

        return response()->json([
            'success' => true,
            'data' => $wallet,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/wallets/{type}/transactions",
     *     summary="Get user wallet transactions",
     *     description="Retrieve paginated transaction history for the authenticated user's specific wallet",
     *     operationId="getUserWalletTransactions",
     *     tags={"User Wallets"},
     *     security={{"sanctum": {}}},
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
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, description="Filter direction (credit, debit)", @OA\Schema(type="string", enum={"credit", "debit"})),
     *     @OA\Parameter(name="filter[action]", in="query", required=false, description="Filter action", @OA\Schema(type="string")),
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
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function transactions(string $type): JsonResponse
    {
        $user = auth()->user();
        $wallet = $this->walletService->getOrCreateWallet($user, $type);

        $perPage = min((int) request()->get('per_page', 15), 100);

        $query = WalletTransaction::where('wallet_id', $wallet->id)->where('user_id', $user->id);

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
     *     path="/api/user/wallets/transactions",
     *     summary="Get all wallet transactions for authenticated user",
     *     description="Retrieve paginated transaction history across all credit wallets for the authenticated user",
     *     operationId="getUserAllWalletTransactions",
     *     tags={"User Wallets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
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
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function allTransactions(): JsonResponse
    {
        $user = auth()->user();
        $perPage = min((int) request()->get('per_page', 15), 100);

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
     *     path="/api/user/wallets/usages",
     *     summary="Get credit usage logs for authenticated user",
     *     description="Retrieve paginated credit usage logs (e.g. AI agent tasks, prompts) for authenticated user",
     *     operationId="getUserWalletUsages",
     *     tags={"User Wallets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="filter[wallet_type]", in="query", required=false, description="Filter by wallet type (basic, premium)", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[reference_id]", in="query", required=false, description="Filter reference ID", @OA\Schema(type="string")),
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
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function usages(): JsonResponse
    {
        $user = auth()->user();
        $perPage = min((int) request()->get('per_page', 15), 100);

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
}
