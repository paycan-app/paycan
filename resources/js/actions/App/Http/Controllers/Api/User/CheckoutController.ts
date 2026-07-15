import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
export const preview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(options),
    method: 'get',
})

preview.definition = {
    methods: ["get","head"],
    url: '/api/user/checkout/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
preview.url = (options?: RouteQueryOptions) => {
    return preview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
preview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
preview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preview.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
const previewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
previewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::preview
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
previewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

preview.form = previewForm

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::create
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

create.definition = {
    methods: ["post"],
    url: '/api/user/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::create
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::create
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
create.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::create
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: create.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::create
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
createForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: create.url(options),
    method: 'post',
})

create.form = createForm

const CheckoutController = { portal, cancel, preview, create }

export default CheckoutController