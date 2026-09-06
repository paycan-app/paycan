<?php

namespace App\Http\Controllers\Api\Shared;

/**
 * Shared OpenAPI schemas used across both Admin and User APIs.
 */
class SharedSchemas
{
    /**
     * @OA\Schema(
     *     schema="User",
     *     type="object",
     *     title="User",
     *     description="User model",
     *     required={"id", "name", "email"},
     *
     *     @OA\Property(property="id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6", description="User ID (ULID or custom string)"),
     *     @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
     *     @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
     *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Email verification timestamp"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Account creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function userSchema() {}

    /**
     * @OA\Schema(
     *     schema="Product",
     *     type="object",
     *     title="Product",
     *     description="Product model",
     *     required={"id", "title", "slug", "type", "is_active"},
     *
     *     @OA\Property(property="id", type="string", example="01HKXZ8M9PQRS2T3U4V5W6X7Y8", description="Product ID (ULID or custom string)"),
     *     @OA\Property(property="title", type="string", example="Premium Subscription", description="Product title"),
     *     @OA\Property(property="slug", type="string", example="premium-subscription", description="Product URL slug"),
     *     @OA\Property(property="description", type="string", nullable=true, example="Access to premium features", description="Product description"),
     *     @OA\Property(property="type", type="string", enum={"physical", "digital", "service", "subscription"}, example="subscription", description="Product type"),
     *     @OA\Property(property="image", type="string", nullable=true, example="https://example.com/image.jpg", description="Product image URL"),
     *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether the product is active"),
     *     @OA\Property(property="meta", type="object", nullable=true, description="Additional product metadata"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp"),
     *     @OA\Property(
     *         property="prices",
     *         type="array",
     *         description="Product pricing options",
     *
     *         @OA\Items(ref="#/components/schemas/ProductPrice")
     *     )
     * )
     */
    public function productSchema() {}

    /**
     * @OA\Schema(
     *     schema="ProductPrice",
     *     type="object",
     *     title="ProductPrice",
     *     description="Product pricing model",
     *     required={"id", "product_id", "amount", "currency", "billing_period", "is_active"},
     *
     *     @OA\Property(property="id", type="string", example="01HKXZ9N0QRST3U4V5W6X7Y8Z9", description="Price ID (ULID or custom string)"),
     *     @OA\Property(property="product_id", type="string", example="01HKXZ8M9PQRS2T3U4V5W6X7Y8", description="Associated product ID"),
     *     @OA\Property(property="amount", type="number", format="decimal", example=29.99, description="Price amount"),
     *     @OA\Property(property="currency", type="string", example="USD", description="Currency code"),
     *     @OA\Property(property="billing_period", type="string", enum={"once", "daily", "weekly", "monthly", "yearly"}, example="monthly", description="Billing period"),
     *     @OA\Property(property="billing_interval", type="integer", example=1, description="Billing interval (e.g., every 1 month)"),
     *     @OA\Property(property="credit_allocations", type="object", nullable=true, example={"basic": 5000, "premium": 100}, description="Allocated credits per wallet type"),
     *     @OA\Property(property="credit_renewal_policy", type="string", enum={"accumulate", "reset"}, example="accumulate", description="Whether unused credits rollover or reset on recurring billing"),
     *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether the price is active"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function productPriceSchema() {}

    /**
     * @OA\Schema(
     *     schema="Order",
     *     type="object",
     *     title="Order",
     *     description="Order model",
     *     required={"id", "user_id", "product_id", "product_price_id", "amount", "currency", "status"},
     *
     *     @OA\Property(property="id", type="integer", example=1, description="Order ID (auto-incrementing)"),
     *     @OA\Property(property="user_id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6", description="Customer user ID"),
     *     @OA\Property(property="product_id", type="string", example="01HKXZ8M9PQRS2T3U4V5W6X7Y8", description="Product ID"),
     *     @OA\Property(property="product_price_id", type="string", example="01HKXZ9N0QRST3U4V5W6X7Y8Z9", description="Product price ID"),
     *     @OA\Property(property="amount", type="number", format="decimal", example=29.99, description="Order amount"),
     *     @OA\Property(property="currency", type="string", example="USD", description="Currency code"),
     *     @OA\Property(property="status", type="string", enum={"pending", "processing", "completed", "failed", "cancelled", "refunded"}, example="completed", description="Order status"),
     *     @OA\Property(property="gateway", type="string", enum={"stripe", "paypal"}, example="stripe", description="Payment gateway used"),
     *     @OA\Property(property="gateway_order_id", type="string", nullable=true, description="Gateway-specific order ID"),
     *     @OA\Property(property="quantity", type="integer", example=1, description="Order quantity"),
     *     @OA\Property(property="customer_note", type="string", nullable=true, description="Customer note"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function orderSchema() {}

    /**
     * @OA\Schema(
     *     schema="Subscription",
     *     type="object",
     *     title="Subscription",
     *     description="Subscription model",
     *     required={"id", "user_id", "product_id", "product_price_id", "status", "gateway"},
     *
     *     @OA\Property(property="id", type="integer", example=1, description="Subscription ID (auto-incrementing)"),
     *     @OA\Property(property="user_id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6", description="Customer user ID"),
     *     @OA\Property(property="product_id", type="string", example="01HKXZ8M9PQRS2T3U4V5W6X7Y8", description="Product ID"),
     *     @OA\Property(property="product_price_id", type="string", example="01HKXZ9N0QRST3U4V5W6X7Y8Z9", description="Product price ID"),
     *     @OA\Property(property="status", type="string", enum={"active", "trialing", "past_due", "canceled", "incomplete", "incomplete_expired"}, example="active", description="Subscription status"),
     *     @OA\Property(property="gateway", type="string", enum={"stripe", "paypal"}, example="stripe", description="Payment gateway"),
     *     @OA\Property(property="gateway_subscription_id", type="string", nullable=true, description="Gateway-specific subscription ID"),
     *     @OA\Property(property="current_period_start", type="string", format="date-time", nullable=true, description="Current billing period start"),
     *     @OA\Property(property="current_period_end", type="string", format="date-time", nullable=true, description="Current billing period end (alias, unified display)"),
     *     @OA\Property(property="next_billing_date", type="string", format="date-time", nullable=true, description="Next billing date"),
     *     @OA\Property(property="ends_at", type="string", format="date-time", nullable=true, description="Cancellation effective date"),
     *     @OA\Property(property="canceled_at", type="string", format="date-time", nullable=true, description="Cancellation timestamp"),
     *     @OA\Property(property="trial_ends_at", type="string", format="date-time", nullable=true, description="Trial end date"),
     *     @OA\Property(property="can_resume", type="boolean", example=true, description="Whether the subscription can be resumed"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function subscriptionSchema() {}

    /**
     * @OA\Schema(
     *     schema="Transaction",
     *     type="object",
     *     title="Transaction",
     *     description="Transaction model",
     *     required={"id", "order_id", "amount", "currency", "status", "gateway"},
     *
     *     @OA\Property(property="id", type="integer", example=1, description="Transaction ID (auto-incrementing)"),
     *     @OA\Property(property="order_id", type="integer", example=1, description="Related order ID"),
     *     @OA\Property(property="amount", type="number", format="decimal", example=29.99, description="Transaction amount"),
     *     @OA\Property(property="currency", type="string", example="USD", description="Currency code"),
     *     @OA\Property(property="status", type="string", enum={"pending", "processing", "completed", "failed", "refunded"}, example="completed", description="Transaction status"),
     *     @OA\Property(property="gateway", type="string", enum={"stripe", "paypal"}, example="stripe", description="Payment gateway"),
     *     @OA\Property(property="gateway_transaction_id", type="string", nullable=true, description="Gateway-specific transaction ID"),
     *     @OA\Property(property="type", type="string", enum={"payment", "refund"}, example="payment", description="Transaction type"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function transactionSchema() {}

    /**
     * @OA\Schema(
     *     schema="Fulfillment",
     *     type="object",
     *     title="Fulfillment",
     *     description="Order fulfillment model",
     *     required={"id", "order_id", "status"},
     *
     *     @OA\Property(property="id", type="integer", example=1, description="Fulfillment ID"),
     *     @OA\Property(property="order_id", type="integer", example=1, description="Related order ID"),
     *     @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "failed"}, example="delivered", description="Fulfillment status"),
     *     @OA\Property(property="tracking_number", type="string", nullable=true, example="1234567890", description="Shipping tracking number"),
     *     @OA\Property(property="carrier", type="string", nullable=true, example="USPS", description="Shipping carrier"),
     *     @OA\Property(property="notes", type="string", nullable=true, description="Fulfillment notes"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function fulfillmentSchema() {}

    /**
     * @OA\Schema(
     *     schema="PaginationLinks",
     *     type="object",
     *     title="Pagination Links",
     *     description="Pagination navigation links",
     *
     *     @OA\Property(property="first", type="string", nullable=true, example="http://example.com/api/orders?page=1", description="First page URL"),
     *     @OA\Property(property="last", type="string", nullable=true, example="http://example.com/api/orders?page=10", description="Last page URL"),
     *     @OA\Property(property="prev", type="string", nullable=true, example="http://example.com/api/orders?page=1", description="Previous page URL"),
     *     @OA\Property(property="next", type="string", nullable=true, example="http://example.com/api/orders?page=3", description="Next page URL")
     * )
     */
    public function paginationLinksSchema() {}

    /**
     * @OA\Schema(
     *     schema="PaginationMeta",
     *     type="object",
     *     title="Pagination Metadata",
     *     description="Pagination metadata",
     *
     *     @OA\Property(property="current_page", type="integer", example=2, description="Current page number"),
     *     @OA\Property(property="from", type="integer", nullable=true, example=16, description="Starting item number"),
     *     @OA\Property(property="last_page", type="integer", example=10, description="Last page number"),
     *     @OA\Property(property="path", type="string", example="http://example.com/api/orders", description="Base URL"),
     *     @OA\Property(property="per_page", type="integer", example=15, description="Items per page"),
     *     @OA\Property(property="to", type="integer", nullable=true, example=30, description="Ending item number"),
     *     @OA\Property(property="total", type="integer", example=150, description="Total items"),
     *     @OA\Property(
     *         property="links",
     *         type="array",
     *         description="Page links array",
     *
     *         @OA\Items(
     *             type="object",
     *
     *             @OA\Property(property="url", type="string", nullable=true, example="http://example.com/api/orders?page=2"),
     *             @OA\Property(property="label", type="string", example="2"),
     *             @OA\Property(property="active", type="boolean", example=true)
     *         )
     *     )
     * )
     */
    public function paginationMetaSchema() {}

    /**
     * @OA\Schema(
     *     schema="ErrorResponse",
     *     type="object",
     *     title="Error Response",
     *     description="Standard error response",
     *
     *     @OA\Property(property="message", type="string", example="Unauthenticated.", description="Error message"),
     *     @OA\Property(
     *         property="errors",
     *         type="object",
     *         nullable=true,
     *         description="Validation errors (if applicable)",
     *
     *         @OA\AdditionalProperties(
     *             type="array",
     *
     *             @OA\Items(type="string")
     *         )
     *     )
     * )
     */
    public function errorResponseSchema() {}

    /**
     * @OA\Schema(
     *     schema="SuccessResponse",
     *     type="object",
     *     title="Success Response",
     *     description="Standard success response",
     *
     *     @OA\Property(property="message", type="string", example="Operation completed successfully", description="Success message"),
     *     @OA\Property(property="data", type="object", nullable=true, description="Response data (if applicable)")
     * )
     */
    public function successResponseSchema() {}

    /**
     * @OA\Schema(
     *     schema="ValidationErrorResponse",
     *     type="object",
     *     title="Validation Error Response",
     *     description="Validation error response with field-specific errors",
     *
     *     @OA\Property(property="message", type="string", example="The given data was invalid.", description="Error message"),
     *     @OA\Property(
     *         property="errors",
     *         type="object",
     *         description="Field-specific validation errors",
     *
     *         @OA\AdditionalProperties(
     *             type="array",
     *
     *             @OA\Items(type="string", example="The email field is required.")
     *         ),
     *         example={
     *             "email": {"The email field is required."},
     *             "name": {"The name field is required."}
     *         }
     *     )
     * )
     */
    public function validationErrorResponseSchema() {}

    /**
     * @OA\Schema(
     *     schema="Wallet",
     *     type="object",
     *     title="Wallet",
     *     description="User credit wallet model",
     *     required={"id", "user_id", "type", "balance", "currency", "is_active"},
     *
     *     @OA\Property(property="id", type="string", example="01JM7A9B8C7D6E5F4G3H2J1K0L", description="Wallet ID (ULID)"),
     *     @OA\Property(property="user_id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6", description="User ID"),
     *     @OA\Property(property="type", type="string", enum={"basic", "premium"}, example="basic", description="Wallet type (e.g. basic, premium, or custom)"),
     *     @OA\Property(property="balance", type="number", format="float", example=1250.0000, description="Current credit balance"),
     *     @OA\Property(property="currency", type="string", example="credits", description="Unit of account"),
     *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether wallet is active"),
     *     @OA\Property(property="meta", type="object", nullable=true, description="Additional wallet metadata"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     * )
     */
    public function walletSchema() {}

    /**
     * @OA\Schema(
     *     schema="WalletTransaction",
     *     type="object",
     *     title="WalletTransaction",
     *     description="Credit ledger transaction & usage log entry",
     *     required={"id", "wallet_id", "user_id", "type", "action", "amount", "balance_after"},
     *
     *     @OA\Property(property="id", type="string", example="01JM7B1C2D3E4F5G6H7J8K9L0M", description="Transaction ID (ULID)"),
     *     @OA\Property(property="wallet_id", type="string", example="01JM7A9B8C7D6E5F4G3H2J1K0L", description="Associated wallet ID"),
     *     @OA\Property(property="user_id", type="string", example="01HKXZ7K5QGXP0B1J2R3T4V5W6", description="Associated user ID"),
     *     @OA\Property(property="type", type="string", enum={"credit", "debit"}, example="debit", description="Transaction direction: credit (added) or debit (deducted)"),
     *     @OA\Property(property="action", type="string", enum={"usage", "subscription_grant", "subscription_renewal", "order_purchase", "manual_adjustment", "subscription_reset"}, example="usage", description="Business event that produced the transaction"),
     *     @OA\Property(property="amount", type="number", format="float", example=2.5, description="Credit amount"),
     *     @OA\Property(property="balance_after", type="number", format="float", example=1247.5, description="Wallet balance after transaction"),
     *     @OA\Property(property="reference_id", type="string", nullable=true, example="task_run_891", description="External task, agent run, order, or subscription ID"),
     *     @OA\Property(property="description", type="string", nullable=true, example="GPT-4o agent execution", description="Human-readable description"),
     *     @OA\Property(property="meta", type="object", nullable=true, example={"model": "gpt-4o", "tokens": 1200}, description="Execution or usage metadata (model, tokens, etc.)"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp"),
     *     @OA\Property(property="wallet", ref="#/components/schemas/Wallet", description="Associated wallet details")
     * )
     */
    public function walletTransactionSchema() {}

    /**
     * @OA\Schema(
     *     schema="DeductCreditsResponse",
     *     type="object",
     *     title="Deduct Credits Response",
     *     description="Successful credit deduction result",
     *
     *     @OA\Property(property="success", type="boolean", example=true),
     *     @OA\Property(property="message", type="string", example="Credits deducted successfully"),
     *     @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="transaction", ref="#/components/schemas/WalletTransaction"),
     *         @OA\Property(
     *             property="wallet",
     *             type="object",
     *             @OA\Property(property="type", type="string", example="premium"),
     *             @OA\Property(property="balance", type="number", format="float", example=47.5)
     *         )
     *     )
     * )
     */
    public function deductCreditsResponseSchema() {}

    /**
     * @OA\Schema(
     *     schema="InsufficientCreditsErrorResponse",
     *     type="object",
     *     title="Insufficient Credits Error Response",
     *     description="Returned when user wallet balance is too low for the requested deduction",
     *
     *     @OA\Property(property="success", type="boolean", example=false),
     *     @OA\Property(property="error", type="string", example="insufficient_credits"),
     *     @OA\Property(property="message", type="string", example="Insufficient credits in premium wallet. Current balance: 1.2, required: 2.5."),
     *     @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="wallet_type", type="string", example="premium"),
     *         @OA\Property(property="current_balance", type="number", format="float", example=1.2),
     *         @OA\Property(property="required_amount", type="number", format="float", example=2.5)
     *     )
     * )
     */
    public function insufficientCreditsErrorResponseSchema() {}
}
