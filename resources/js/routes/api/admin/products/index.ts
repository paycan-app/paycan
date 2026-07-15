import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
import prices from './prices'
/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/admin/products',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:113
* @route '/api/admin/products'
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
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:240
* @route '/api/admin/products'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/admin/products',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:240
* @route '/api/admin/products'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:240
* @route '/api/admin/products'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:240
* @route '/api/admin/products'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:240
* @route '/api/admin/products'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
*/
export const show = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/admin/products/{product}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
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
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
*/
show.get = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
*/
show.head = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
*/
const showForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
*/
showForm.get = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::show
* @see app/Http/Controllers/Api/Admin/ProductController.php:190
* @route '/api/admin/products/{product}'
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

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:300
* @route '/api/admin/products/{product}'
*/
export const update = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/admin/products/{product}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:300
* @route '/api/admin/products/{product}'
*/
update.url = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:300
* @route '/api/admin/products/{product}'
*/
update.put = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:300
* @route '/api/admin/products/{product}'
*/
const updateForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:300
* @route '/api/admin/products/{product}'
*/
updateForm.put = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:338
* @route '/api/admin/products/{product}'
*/
export const destroy = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/admin/products/{product}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:338
* @route '/api/admin/products/{product}'
*/
destroy.url = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:338
* @route '/api/admin/products/{product}'
*/
destroy.delete = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:338
* @route '/api/admin/products/{product}'
*/
const destroyForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:338
* @route '/api/admin/products/{product}'
*/
destroyForm.delete = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const products = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    prices: Object.assign(prices, prices),
}

export default products