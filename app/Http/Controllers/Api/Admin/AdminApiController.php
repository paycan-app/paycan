<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="PayCan Admin API Documentation",
 *      description="Admin API for PayCan payment processing platform. Manage products, orders, subscriptions, and system configuration. Requires API Key authentication.",
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
 *      securityScheme="apiKeyHeader",
 *      type="apiKey",
 *      in="header",
 *      name="X-API-Key",
 *      description="API Secret Key in X-API-Key header. Get from Admin Panel > Settings > API Secret Key. Format: X-API-Key: pk_xxxxx"
 * )
 * @OA\SecurityScheme(
 *      securityScheme="apiKeyQuery",
 *      type="apiKey",
 *      in="query",
 *      name="api_key",
 *      description="API Secret Key in query parameter. Only works in local development (APP_ENV=local) for debugging. Format: ?api_key=pk_xxxxx (NOT available in production)"
 * )
 */
abstract class AdminApiController extends Controller
{
    //
}
