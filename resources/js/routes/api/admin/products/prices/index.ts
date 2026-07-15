import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
export const index = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/admin/products/{product}/prices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
index.url = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
index.get = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
index.head = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
const indexForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
indexForm.get = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::index
* @see app/Http/Controllers/Api/Admin/ProductController.php:381
* @route '/api/admin/products/{product}/prices'
*/
indexForm.head = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
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
* @see app/Http/Controllers/Api/Admin/ProductController.php:435
* @route '/api/admin/products/{product}/prices'
*/
export const store = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/admin/products/{product}/prices',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:435
* @route '/api/admin/products/{product}/prices'
*/
store.url = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:435
* @route '/api/admin/products/{product}/prices'
*/
store.post = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:435
* @route '/api/admin/products/{product}/prices'
*/
const storeForm = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::store
* @see app/Http/Controllers/Api/Admin/ProductController.php:435
* @route '/api/admin/products/{product}/prices'
*/
storeForm.post = (args: { product: string | { id: string } } | [product: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:509
* @route '/api/admin/products/{product}/prices/{price}'
*/
export const update = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/admin/products/{product}/prices/{price}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:509
* @route '/api/admin/products/{product}/prices/{price}'
*/
update.url = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            product: args[0],
            price: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product: typeof args.product === 'object'
        ? args.product.id
        : args.product,
        price: typeof args.price === 'object'
        ? args.price.id
        : args.price,
    }

    return update.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace('{price}', parsedArgs.price.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:509
* @route '/api/admin/products/{product}/prices/{price}'
*/
update.put = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::update
* @see app/Http/Controllers/Api/Admin/ProductController.php:509
* @route '/api/admin/products/{product}/prices/{price}'
*/
const updateForm = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/Api/Admin/ProductController.php:509
* @route '/api/admin/products/{product}/prices/{price}'
*/
updateForm.put = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/Api/Admin/ProductController.php:566
* @route '/api/admin/products/{product}/prices/{price}'
*/
export const destroy = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/admin/products/{product}/prices/{price}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:566
* @route '/api/admin/products/{product}/prices/{price}'
*/
destroy.url = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            product: args[0],
            price: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product: typeof args.product === 'object'
        ? args.product.id
        : args.product,
        price: typeof args.price === 'object'
        ? args.price.id
        : args.price,
    }

    return destroy.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace('{price}', parsedArgs.price.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:566
* @route '/api/admin/products/{product}/prices/{price}'
*/
destroy.delete = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Admin\ProductController::destroy
* @see app/Http/Controllers/Api/Admin/ProductController.php:566
* @route '/api/admin/products/{product}/prices/{price}'
*/
const destroyForm = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/Api/Admin/ProductController.php:566
* @route '/api/admin/products/{product}/prices/{price}'
*/
destroyForm.delete = (args: { product: string | { id: string }, price: string | { id: string } } | [product: string | { id: string }, price: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const prices = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default prices