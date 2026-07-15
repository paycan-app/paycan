<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\StoreUserRequest;
use App\Http\Requests\Api\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Admin Users",
 *     description="Admin user management endpoints"
 * )
 */
class UserController extends AdminApiController
{
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="List all users (Admin)",
     *     description="Retrieve paginated and filterable list of all users (admin only)",
     *     operationId="listAllUsers",
     *     tags={"Admin Users"},
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
     *         name="filter[email]",
     *         in="query",
     *         description="Filter by email (partial match)",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by name (partial match)",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[email_verified]",
     *         in="query",
     *         description="Filter by email verification status",
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
     *         @OA\Schema(type="string", example="orders,subscriptions,transactions")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort users (prefix with - for descending)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="-created_at", enum={"created_at", "-created_at", "name", "-name", "email", "-email"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
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
        $query = User::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('email'),
                AllowedFilter::partial('name'),
                AllowedFilter::callback('email_verified', function ($query, $value) {
                    if ($value === 'true' || $value === true || $value === '1') {
                        $query->whereNotNull('email_verified_at');
                    } else {
                        $query->whereNull('email_verified_at');
                    }
                }),
            ])
            ->allowedIncludes([
                'orders',
                'subscriptions',
                'transactions',
                'socialConnections',
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('name'),
                AllowedSort::field('email'),
            ])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{user}",
     *     summary="Get user details (Admin)",
     *     description="Retrieve a specific user by ID (admin only)",
     *     operationId="getAdminUser",
     *     tags={"Admin Users"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User ID",
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
     *         @OA\Schema(type="string", example="orders,subscriptions,transactions")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
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
     *         description="User not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(User $user): JsonResponse
    {
        $user = QueryBuilder::for(User::where('id', $user->id))
            ->allowedIncludes([
                'orders',
                'subscriptions',
                'transactions',
                'socialConnections',
            ])
            ->first();

        return response()->json(['data' => new UserResource($user)]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users",
     *     summary="Create user (Admin)",
     *     description="Create a new user",
     *     operationId="createUser",
     *     tags={"Admin Users"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="SecurePassword123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="SecurePassword123!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return response()->json(['data' => new UserResource($user)], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/users/{user}",
     *     summary="Update user (Admin)",
     *     description="Update a specific user",
     *     operationId="updateUser",
     *     tags={"Admin Users"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User ID",
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
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="NewPassword123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword123!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        // Handle password separately to ensure proper hashing
        if (isset($validated['password'])) {
            $password = $validated['password'];
            unset($validated['password']);
            $user->password = $password; // Will be hashed automatically by 'hashed' cast
        }

        $user->update($validated);

        return response()->json(['data' => new UserResource($user)]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/users/{user}",
     *     summary="Delete user (Admin)",
     *     description="Delete a specific user",
     *     operationId="deleteUser",
     *     tags={"Admin Users"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
