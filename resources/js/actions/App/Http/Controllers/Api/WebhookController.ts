import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WebhookController::stripe
* @see app/Http/Controllers/Api/WebhookController.php:84
* @route '/api/webhooks/stripe'
*/
export const stripe = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stripe.url(options),
    method: 'post',
})

stripe.definition = {
    methods: ["post"],
    url: '/api/webhooks/stripe',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WebhookController::stripe
* @see app/Http/Controllers/Api/WebhookController.php:84
* @route '/api/webhooks/stripe'
*/
stripe.url = (options?: RouteQueryOptions) => {
    return stripe.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WebhookController::stripe
* @see app/Http/Controllers/Api/WebhookController.php:84
* @route '/api/webhooks/stripe'
*/
stripe.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stripe.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WebhookController::stripe
* @see app/Http/Controllers/Api/WebhookController.php:84
* @route '/api/webhooks/stripe'
*/
const stripeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stripe.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WebhookController::stripe
* @see app/Http/Controllers/Api/WebhookController.php:84
* @route '/api/webhooks/stripe'
*/
stripeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stripe.url(options),
    method: 'post',
})

stripe.form = stripeForm

/**
* @see \App\Http\Controllers\Api\WebhookController::paypal
* @see app/Http/Controllers/Api/WebhookController.php:235
* @route '/api/webhooks/paypal'
*/
export const paypal = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: paypal.url(options),
    method: 'post',
})

paypal.definition = {
    methods: ["post"],
    url: '/api/webhooks/paypal',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WebhookController::paypal
* @see app/Http/Controllers/Api/WebhookController.php:235
* @route '/api/webhooks/paypal'
*/
paypal.url = (options?: RouteQueryOptions) => {
    return paypal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WebhookController::paypal
* @see app/Http/Controllers/Api/WebhookController.php:235
* @route '/api/webhooks/paypal'
*/
paypal.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: paypal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WebhookController::paypal
* @see app/Http/Controllers/Api/WebhookController.php:235
* @route '/api/webhooks/paypal'
*/
const paypalForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: paypal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WebhookController::paypal
* @see app/Http/Controllers/Api/WebhookController.php:235
* @route '/api/webhooks/paypal'
*/
paypalForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: paypal.url(options),
    method: 'post',
})

paypal.form = paypalForm

const WebhookController = { stripe, paypal }

export default WebhookController