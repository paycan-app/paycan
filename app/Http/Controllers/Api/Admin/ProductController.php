<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\ProductPriceStoreRequest;
use App\Http\Requests\Api\Admin\ProductPriceUpdateRequest;
use App\Http\Requests\Api\Admin\ProductStoreRequest;
use App\Http\Requests\Api\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Admin Products",
 *     description="Admin product and price management endpoints"
 * )
 */
class ProductController extends AdminApiController
{
    /**
     * @OA\Get(
     *     path="/api/admin/products",
     *     summary="List all products (Admin)",
     *     description="Retrieve paginated and filterable list of all products (admin only)",
     *     operationId="listAllProducts",
     *     tags={"Admin Products"},
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
     *         name="filter[type]",
     *         in="query",
     *         description="Filter by product type",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"digital", "physical", "service", "subscription"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[is_active]",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *
     *         @OA\Schema(type="boolean")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="prices,activePrices")
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
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('is_active'),
            ])
            ->allowedIncludes([
                'prices',
                'activePrices',
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('title'),
            ])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/products/{product}",
     *     summary="Get product details (Admin)",
     *     description="Retrieve a specific product by ID (admin only)",
     *     operationId="getAdminProduct",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (comma-separated)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="prices,activePrices")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
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
     *         description="Product not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        $product = QueryBuilder::for(Product::where('id', $product->id))
            ->allowedIncludes([
                'prices',
                'activePrices',
            ])
            ->first();

        return response()->json(['data' => $product]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products",
     *     summary="Create product (Admin)",
     *     description="Create a new product",
     *     operationId="createProduct",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"title", "type"},
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="type", type="string", enum={"digital", "physical", "service", "subscription"}),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Generate slug from title if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        }

        $product = Product::create($validated);

        return response()->json(['data' => $product], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/products/{product}",
     *     summary="Update product (Admin)",
     *     description="Update a specific product",
     *     operationId="updateProduct",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        // Generate slug from title if title is provided but slug is not
        if (isset($validated['title']) && ! isset($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        }

        $product->update($validated);

        return response()->json(['data' => $product]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/{product}",
     *     summary="Delete product (Admin)",
     *     description="Delete a specific product",
     *     operationId="deleteProduct",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     )
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }

    // Price Management Routes

    /**
     * @OA\Get(
     *     path="/api/admin/products/{product}/prices",
     *     summary="List product prices (Admin)",
     *     description="Get all prices for a specific product",
     *     operationId="listProductPrices",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Prices retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function priceIndex(Product $product): JsonResponse
    {
        $prices = $product->prices()->get();

        return response()->json(['data' => $prices]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products/{product}/prices",
     *     summary="Create product price (Admin)",
     *     description="Create a new price for a product",
     *     operationId="createProductPrice",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"amount", "currency", "billing_period"},
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="currency", type="string", example="USD"),
     *             @OA\Property(property="billing_period", type="string", enum={"once", "daily", "weekly", "monthly", "yearly"}),
     *             @OA\Property(property="trial_days", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Price created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function priceStore(ProductPriceStoreRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        // Generate title if not provided
        if (empty($validated['title'])) {
            $validated['title'] = ucfirst($validated['billing_period']).' - '.$validated['currency'].' '.$validated['amount'];
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        }

        $price = $product->prices()->create($validated);

        return response()->json(['data' => $price], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/products/{product}/prices/{price}",
     *     summary="Update product price (Admin)",
     *     description="Update a specific price",
     *     operationId="updateProductPrice",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="price",
     *         in="path",
     *         description="Price ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="billing_period", type="string"),
     *             @OA\Property(property="trial_days", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Price updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function priceUpdate(ProductPriceUpdateRequest $request, Product $product, ProductPrice $price): JsonResponse
    {
        // Ensure price belongs to product
        if ($price->product_id !== $product->id) {
            return response()->json(['error' => 'Price not found'], 404);
        }

        $validated = $request->validated();

        // Update slug if title changed but slug didn't
        if (isset($validated['title']) && ! isset($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        }

        $price->update($validated);

        return response()->json(['data' => $price]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/{product}/prices/{price}",
     *     summary="Delete product price (Admin)",
     *     description="Delete a specific price",
     *     operationId="deleteProductPrice",
     *     tags={"Admin Products"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="price",
     *         in="path",
     *         description="Price ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Price deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Price deleted successfully")
     *         )
     *     )
     * )
     */
    public function priceDestroy(Product $product, ProductPrice $price): JsonResponse
    {
        // Ensure price belongs to product
        if ($price->product_id !== $product->id) {
            return response()->json(['error' => 'Price not found'], 404);
        }

        $price->delete();

        return response()->json(['message' => 'Price deleted successfully']);
    }
}
