<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="PayCan User API Documentation",
 *      description="User API for PayCan payment processing platform. Access products, manage orders, subscriptions, and user profile. Requires JWT authentication via Sanctum.",
 *
 *      @OA\Contact(
 *          email="support@paycan.com",
 *          name="PayCan Support"
 *      ),
 *
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url="/",
 *      description="PayCan API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="sanctum",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      description="USER AUTHENTICATION (Sanctum JWT) - For mobile apps & SPAs. To get a user token for testing, use the Admin API endpoint: POST /api/admin/users/sync (see Admin API documentation). Format: Bearer <user_jwt_token>"
 * )
 */
abstract class UserApiController extends Controller
{
    //
}
