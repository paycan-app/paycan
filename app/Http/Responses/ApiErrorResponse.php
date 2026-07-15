<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiErrorResponse
{
    /**
     * Create a standardized error response
     */
    public static function make(
        string $message,
        int $statusCode = 400,
        ?string $errorCode = null,
        ?array $errors = null,
        ?array $meta = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode) {
            $response['error_code'] = $errorCode;
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        if ($meta) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Create a not found error response
     */
    public static function notFound(string $resource = 'Resource'): JsonResponse
    {
        return self::make(
            message: "{$resource} not found",
            statusCode: 404,
            errorCode: 'resource_not_found'
        );
    }

    /**
     * Create an unauthorized error response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::make(
            message: $message,
            statusCode: 401,
            errorCode: 'unauthorized'
        );
    }

    /**
     * Create a forbidden error response
     */
    public static function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return self::make(
            message: $message,
            statusCode: 403,
            errorCode: 'forbidden'
        );
    }

    /**
     * Create a validation error response
     */
    public static function validation(string $message, array $errors): JsonResponse
    {
        return self::make(
            message: $message,
            statusCode: 422,
            errorCode: 'validation_error',
            errors: $errors
        );
    }

    /**
     * Create a server error response
     */
    public static function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return self::make(
            message: $message,
            statusCode: 500,
            errorCode: 'server_error'
        );
    }

    /**
     * Create a bad request error response
     */
    public static function badRequest(string $message, ?string $errorCode = null): JsonResponse
    {
        return self::make(
            message: $message,
            statusCode: 400,
            errorCode: $errorCode ?? 'bad_request'
        );
    }
}
