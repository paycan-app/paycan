import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
export const publicMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publicMethod.url(options),
    method: 'get',
})

publicMethod.definition = {
    methods: ["get","head"],
    url: '/api/user/checkout/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
publicMethod.url = (options?: RouteQueryOptions) => {
    return publicMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
publicMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publicMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
publicMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: publicMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
const publicMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: publicMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
publicMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: publicMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::publicMethod
* @see app/Http/Controllers/Api/User/CheckoutController.php:507
* @route '/api/user/checkout/preview'
*/
publicMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: publicMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

publicMethod.form = publicMethodForm

const preview = {
    public: Object.assign(publicMethod, publicMethod),
}

export default preview