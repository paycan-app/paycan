<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\UserTokenRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Admin User Tokens",
 *     description="Admin endpoints for user synchronization and token generation"
 * )
 */
class UserTokenController extends AdminApiController
{
    /**
     * @OA\Post(
     *      path="/api/admin/users/sync",
     *      operationId="syncUserAndGenerateToken",
     *      tags={"Admin User Tokens"},
     *      summary="Sync user and generate JWT token",
     *      description="Creates or updates a user and returns a JWT token. Requires API key authentication via X-API-Key header. Uses user_id as the unique identifier. For NEW users: name and email are required. For EXISTING users: updates are optional.
     *      SECURITY WARNING: This endpoint is intended for server-to-server communication only. The API Key allows generating tokens for ANY user. Never expose the API Key in client-side code.",
     *      security={{"apiKeyHeader": {}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"user_id"},
     *
     *              @OA\Property(property="user_id", type="string", example="usr_12345", description="User ID - will be used as the user's primary key"),
     *              @OA\Property(
     *                  property="user",
     *                  type="object",
     *                  description="User data - REQUIRED for new users (must include name and email), OPTIONAL for existing users (updates provided fields)",
     *                  @OA\Property(property="name", type="string", example="John Doe", description="User's name (required for new users, optional for updates)"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email (required for new users, optional for updates)")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Token generated successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="token", type="string", example="1|abc123def456...")
     *          )
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthorized - Invalid or missing API key"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function generateToken(UserTokenRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $validated['user_id'];
        $userData = $validated['user'] ?? null;

        // Find user by id
        $user = User::find($userId);

        if ($user) {
            // User exists - update if user data provided
            if ($userData) {
                // Validate unique email (excluding current user)
                if (isset($userData['email']) && $userData['email']) {
                    $emailExists = User::where('email', $userData['email'])
                        ->where('id', '!=', $user->id)
                        ->exists();

                    if ($emailExists) {
                        throw ValidationException::withMessages([
                            'user.email' => ['The email has already been taken.'],
                        ]);
                    }
                }

                $updateData = [];
                if (isset($userData['name'])) {
                    $updateData['name'] = $userData['name'];
                }
                if (isset($userData['email'])) {
                    $updateData['email'] = $userData['email'];
                }

                if (! empty($updateData)) {
                    $user->update($updateData);
                    $user->touch(); // Update updated_at timestamp
                }
            }
        } else {
            // User doesn't exist - create new user (email and name required by validation)
            $user = User::create([
                'id' => $userId,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt(Str::random(32)), // Random password
            ]);
        }

        // Generate token
        $token = $user->createToken('external-auth')->plainTextToken;

        return response()->json([
            // We do not need to share user details with the client
            // 'data' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at']),
            'token' => $token,
        ]);
    }
}
