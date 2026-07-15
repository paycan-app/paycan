<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication and token management"
 * )
 */
class AuthController extends Controller
{




    /**
     * @disabled-OA\Post(
     *     path="/api/auth/logout",
     *     summary="User logout",
     *     description="Revoke current access token and logout user",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *
     *     @disabled-Response(
     *         response=200,
     *         description="Logout successful",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @disabled-JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * @disabled-OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     description="Authenticate user and generate access token",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *
     *     @disabled-RequestBody(
     *         required=true,
     *         description="User login credentials",
     *
     *         @disabled-JsonContent(
     *             required={"email", "password"},
     *
     *             @disabled-Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
     *             @disabled-Property(property="password", type="string", format="password", example="password123", description="User's password")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="Login successful"),
     *             @disabled-Property(property="data", ref="#/components/schemas/User"),
     *             @disabled-Property(property="token", type="string", example="1|abc123def456...", description="API access token"),
     *             @disabled-Property(property="token_type", type="string", example="Bearer", description="Token type")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=422,
     *         description="Invalid credentials",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="The provided credentials are incorrect."),
     *             @disabled-Property(
     *                 property="errors",
     *                 type="object",
     *                 @disabled-Property(
     *                     property="email",
     *                     type="array",
     *
     *                     @disabled-Items(type="string", example="The provided credentials are incorrect.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @disabled-OA\Post(
     *     path="/api/auth/register",
     *     summary="User registration",
     *     description="Register a new user account and generate access token",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *
     *     @disabled-RequestBody(
     *         required=true,
     *         description="User registration data",
     *
     *         @disabled-JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *
     *             @disabled-Property(property="name", type="string", example="John Doe", description="User's full name"),
     *             @disabled-Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
     *             @disabled-Property(property="password", type="string", format="password", example="password123", description="User's password (min 8 characters)"),
     *             @disabled-Property(property="password_confirmation", type="string", format="password", example="password123", description="Password confirmation")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=201,
     *         description="Registration successful",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="Registration successful"),
     *             @disabled-Property(property="data", ref="#/components/schemas/User"),
     *             @disabled-Property(property="access_token", type="string", example="1|abc123def456...", description="API access token"),
     *             @disabled-Property(property="token_type", type="string", example="Bearer", description="Token type")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=422,
     *         description="Validation errors",
     *
     *         @disabled-JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'data' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get current user",
     *     description="Retrieve authenticated user information",
     *     operationId="getCurrentUser",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/User")
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
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh access token",
     *     description="Generate a new access token and revoke the current one",
     *     operationId="refreshToken",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="token", type="string", example="1|abc123def456...", description="New API access token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer", description="Token type")
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
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }






    /**
     * @disabled-OA\Post(
     *     path="/api/auth/token",
     *     summary="Create API token",
     *     description="Generate an API token for authenticated user (alternative to login for existing sessions)",
     *     operationId="createToken",
     *     tags={"Authentication"},
     *
     *     @disabled-RequestBody(
     *         required=true,
     *         description="Token creation data",
     *
     *         @disabled-JsonContent(
     *             required={"email", "password"},
     *
     *             @disabled-Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
     *             @disabled-Property(property="password", type="string", format="password", example="password123", description="User's password"),
     *             @disabled-Property(property="token_name", type="string", example="Mobile App", description="Optional token name for identification")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=200,
     *         description="Token created successfully",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="Token created successfully"),
     *             @disabled-Property(property="token", type="string", example="1|abc123def456...", description="API access token"),
     *             @disabled-Property(property="token_type", type="string", example="Bearer", description="Token type")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=401,
     *         description="Invalid credentials",
     *
     *         @disabled-JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function createToken(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'token_name' => 'sometimes|string|max:255',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => 'Invalid credentials',
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $user = Auth::user();
        $tokenName = $request->get('token_name', 'API Token');
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Token created successfully',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @disabled-OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Send password reset link",
     *     description="Send a password reset link to the user's email address",
     *     operationId="forgotPassword",
     *     tags={"Authentication"},
     *
     *     @disabled-RequestBody(
     *         required=true,
     *         description="Password reset request",
     *
     *         @disabled-JsonContent(
     *             required={"email"},
     *
     *             @disabled-Property(property="email", type="string", format="email", example="user@example.com", description="User's email address")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=200,
     *         description="Password reset link sent",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="Password reset link sent to your email")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=422,
     *         description="Validation errors",
     *
     *         @disabled-JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent to your email',
            ]);
        }

        return response()->json([
            'message' => 'Unable to send password reset link',
            'error' => __($status),
        ], 422);
    }

    /**
     * @disabled-OA\Post(
     *     path="/api/auth/reset-password",
     *     summary="Reset password",
     *     description="Reset user password using the reset token",
     *     operationId="resetPassword",
     *     tags={"Authentication"},
     *
     *     @disabled-RequestBody(
     *         required=true,
     *         description="Password reset data",
     *
     *         @disabled-JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *
     *             @disabled-Property(property="token", type="string", example="abc123def456...", description="Password reset token"),
     *             @disabled-Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
     *             @disabled-Property(property="password", type="string", format="password", example="newpassword123", description="New password (min 8 characters)"),
     *             @disabled-Property(property="password_confirmation", type="string", format="password", example="newpassword123", description="Password confirmation")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=200,
     *         description="Password reset successful",
     *
     *         @disabled-JsonContent(
     *
     *             @disabled-Property(property="message", type="string", example="Password reset successful")
     *         )
     *     ),
     *
     *     @disabled-Response(
     *         response=422,
     *         description="Validation errors or invalid token",
     *
     *         @disabled-JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successful',
            ]);
        }

        return response()->json([
            'message' => 'Unable to reset password',
            'error' => __($status),
        ], 422);
    }
}
