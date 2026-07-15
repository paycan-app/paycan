import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
import preview from './preview'
import create from './create'
/**
* @see \App\Http\Controllers\Api\User\CheckoutController::portal
* @see app/Http/Controllers/Api/User/CheckoutController.php:241
* @route '/api/user/checkout/portal'
*/
export const portal = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: portal.url(options),
    method: 'post',
})

portal.definition = {
    methods: ["post"],
    url: '/api/user/checkout/portal',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::portal
* @see app/Http/Controllers/Api/User/CheckoutController.php:241
* @route '/api/user/checkout/portal'
*/
portal.url = (options?: RouteQueryOptions) => {
    return portal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::portal
* @see app/Http/Controllers/Api/User/CheckoutController.php:241
* @route '/api/user/checkout/portal'
*/
portal.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: portal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::portal
* @see app/Http/Controllers/Api/User/CheckoutController.php:241
* @route '/api/user/checkout/portal'
*/
const portalForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: portal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::portal
* @see app/Http/Controllers/Api/User/CheckoutController.php:241
* @route '/api/user/checkout/portal'
*/
portalForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: portal.url(options),
    method: 'post',
})

portal.form = portalForm

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::cancel
* @see app/Http/Controllers/Api/User/CheckoutController.php:652
* @route '/api/user/checkout/{order}/cancel'
*/
export const cancel = (args: { order: string | number } | [order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/api/user/checkout/{order}/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::cancel
* @see app/Http/Controllers/Api/User/CheckoutController.php:652
* @route '/api/user/checkout/{order}/cancel'
*/
cancel.url = (args: { order: string | number } | [order: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: args.order,
    }

    return cancel.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::cancel
* @see app/Http/Controllers/Api/User/CheckoutController.php:652
* @route '/api/user/checkout/{order}/cancel'
*/
cancel.post = (args: { order: string | number } | [order: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::cancel
* @see app/Http/Controllers/Api/User/CheckoutController.php:652
* @route '/api/user/checkout/{order}/cancel'
*/
const cancelForm = (args: { order: string | number } | [order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::cancel
* @see app/Http/Controllers/Api/User/CheckoutController.php:652
* @route '/api/user/checkout/{order}/cancel'
*/
cancelForm.post = (args: { order: string | number } | [order: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

cancel.form = cancelForm

const checkout = {
    portal: Object.assign(portal, portal),
    cancel: Object.assign(cancel, cancel),
    preview: Object.assign(preview, preview),
    create: Object.assign(create, create),
}

export default checkout