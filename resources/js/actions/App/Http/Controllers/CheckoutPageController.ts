import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/checkout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutPageController::index
* @see app/Http/Controllers/CheckoutPageController.php:18
* @route '/checkout'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const CheckoutPageController = { index }

export default CheckoutPageController