import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/user/products',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::index
* @see app/Http/Controllers/Api/User/ProductController.php:99
* @route '/api/user/products'
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

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
export const show = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/user/products/{product}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
show.url = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { product: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { product: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            product: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product: typeof args.product === 'object'
        ? args.product.id
        : args.product,
    }

    return show.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
show.get = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
show.head = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
const showForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
showForm.get = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\ProductController::show
* @see app/Http/Controllers/Api/User/ProductController.php:177
* @route '/api/user/products/{product}'
*/
showForm.head = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const products = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default products