<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="User Products",
 *     description="User product catalog endpoints"
 * )
 */
class ProductController extends UserApiController
{
    /**
     * @OA\Get(
     *     path="/api/user/products",
     *     summary="Get active products",
     *     description="Retrieve paginated and filterable list of active products with their active prices",
     *     operationId="getUserProducts",
     *     tags={"User Products"},
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
     *         name="filter[type]",
     *         in="query",
     *         description="Filter by product type",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"digital", "physical", "service", "subscription"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (only active prices will be included)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="prices")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort products (prefix with - for descending)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="-created_at", enum={"created_at", "-created_at", "title", "-title"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
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
        $query = QueryBuilder::for(Product::active())
            ->allowedFilters([
                AllowedFilter::exact('type'),
            ])
            ->allowedIncludes([
                'prices',
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('title'),
            ])
            ->defaultSort('title');

        // If prices are included, filter to only active prices
        if (request()->has('include') && str_contains(request('include'), 'prices')) {
            $query->with(['prices' => fn ($q) => $q->where('is_active', true)]);
        }

        $products = $query->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/user/products/{product}",
     *     summary="Get product details",
     *     description="Retrieve a specific active product by ID with its active prices",
     *     operationId="getUserProduct",
     *     tags={"User Products"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", example="prod_123")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (only active prices will be included)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="prices")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
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
     *         description="Product not found or not active",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        // Only show active products
        if (! $product->is_active) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $query = QueryBuilder::for(Product::active()->where('id', $product->id))
            ->allowedIncludes([
                'prices',
            ]);

        // If prices are included, filter to only active prices
        if (request()->has('include') && str_contains(request('include'), 'prices')) {
            $query->with(['prices' => fn ($q) => $q->where('is_active', true)]);
        }

        $product = $query->first();

        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product]);
    }
}
