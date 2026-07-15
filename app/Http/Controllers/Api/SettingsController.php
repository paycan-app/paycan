<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Admin\AdminApiController;
use App\Http\Requests\Api\SettingsUpdateRequest;
use App\Services\Settings\SettingsManager;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Admin Settings",
 *     description="Admin settings management endpoints"
 * )
 */
class SettingsController extends AdminApiController
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    /**
     * @OA\Get(
     *     path="/api/admin/settings",
     *     summary="Get all settings (Admin)",
     *     description="Retrieve all application settings including private settings (admin only)",
     *     operationId="getAllSettings",
     *     tags={"Admin Settings"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Settings retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Key-value pairs of all settings",
     *                 example={
     *                     "app.name": "PayCan",
     *                     "app.url": "https://paycan.com",
     *                     "stripe.public_key": "pk_test_123",
     *                     "stripe.secret_key": "sk_test_456",
     *                     "paypal.client_id": "AXxxx",
     *                     "mail.from.address": "noreply@paycan.com",
     *                     "mail.from.name": "PayCan"
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Invalid or missing API key",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $settings = $this->settings->getAll(publicOnly: false);

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/settings",
     *     summary="Update settings (Admin)",
     *     description="Update one or more application settings (admin only)",
     *     operationId="updateSettings",
     *     tags={"Admin Settings"},
     *     security={{"apiKeyHeader": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"settings"},
     *
     *             @OA\Property(
     *                 property="settings",
     *                 type="array",
     *                 description="Array of settings to update",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="key", type="string", description="Setting key", example="app.name"),
     *                     @OA\Property(property="value", description="Setting value (can be string, number, boolean, array, or null)", example="PayCan"),
     *                     @OA\Property(property="type", type="string", enum={"string", "integer", "boolean", "array", "encrypted"}, description="Value type (optional, defaults to string)", example="string"),
     *                     @OA\Property(property="is_public", type="boolean", description="Whether setting is publicly accessible (optional, defaults to true)", example=true)
     *                 ),
     *                 example={
     *                     {
     *                         "key": "app.name",
     *                         "value": "PayCan Pro",
     *                         "type": "string",
     *                         "is_public": true
     *                     },
     *                     {
     *                         "key": "stripe.secret_key",
     *                         "value": "sk_live_new_key",
     *                         "type": "encrypted",
     *                         "is_public": false
     *                     },
     *                     {
     *                         "key": "mail.enabled",
     *                         "value": true,
     *                         "type": "boolean",
     *                         "is_public": true
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Settings updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Settings updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Invalid or missing API key",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
    public function update(SettingsUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        foreach ($validated['settings'] as $setting) {
            $this->settings->set(
                $setting['key'],
                $setting['value'] ?? null,
                $setting['type'] ?? 'string',
                $setting['is_public'] ?? true
            );
        }

        $this->settings->clearCache();

        return response()->json([
            'message' => 'Settings updated successfully',
        ]);
    }
}
