import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/user/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::index
* @see app/Http/Controllers/Api/User/OrderController.php:135
* @route '/api/user/orders'
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
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
export const show = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/user/orders/{order}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
show.url = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: typeof args.order === 'object'
        ? args.order.id
        : args.order,
    }

    return show.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
show.get = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
show.head = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
const showForm = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
showForm.get = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::show
* @see app/Http/Controllers/Api/User/OrderController.php:224
* @route '/api/user/orders/{order}'
*/
showForm.head = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
export const downloads = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloads.url(args, options),
    method: 'get',
})

downloads.definition = {
    methods: ["get","head"],
    url: '/api/user/orders/{order}/downloads',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
downloads.url = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: typeof args.order === 'object'
        ? args.order.id
        : args.order,
    }

    return downloads.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
downloads.get = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloads.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
downloads.head = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloads.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
const downloadsForm = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloads.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
downloadsForm.get = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloads.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::downloads
* @see app/Http/Controllers/Api/User/OrderController.php:286
* @route '/api/user/orders/{order}/downloads'
*/
downloadsForm.head = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloads.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

downloads.form = downloadsForm

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
export const licenses = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: licenses.url(args, options),
    method: 'get',
})

licenses.definition = {
    methods: ["get","head"],
    url: '/api/user/orders/{order}/licenses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
licenses.url = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: typeof args.order === 'object'
        ? args.order.id
        : args.order,
    }

    return licenses.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
licenses.get = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: licenses.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
licenses.head = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: licenses.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
const licensesForm = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: licenses.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
licensesForm.get = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: licenses.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\OrderController::licenses
* @see app/Http/Controllers/Api/User/OrderController.php:362
* @route '/api/user/orders/{order}/licenses'
*/
licensesForm.head = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: licenses.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

licenses.form = licensesForm

const orders = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
    downloads: Object.assign(downloads, downloads),
    licenses: Object.assign(licenses, licenses),
}

export default orders