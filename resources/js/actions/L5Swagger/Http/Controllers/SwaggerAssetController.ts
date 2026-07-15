import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
const indexef42a28543f37836334b0901d8709275 = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexef42a28543f37836334b0901d8709275.url(args, options),
    method: 'get',
})

indexef42a28543f37836334b0901d8709275.definition = {
    methods: ["get","head"],
    url: '/docs/admin/asset/{asset}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
indexef42a28543f37836334b0901d8709275.url = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { asset: args }
    }

    if (Array.isArray(args)) {
        args = {
            asset: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        asset: args.asset,
    }

    return indexef42a28543f37836334b0901d8709275.definition.url
            .replace('{asset}', parsedArgs.asset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
indexef42a28543f37836334b0901d8709275.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexef42a28543f37836334b0901d8709275.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
indexef42a28543f37836334b0901d8709275.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexef42a28543f37836334b0901d8709275.url(args, options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
const indexef42a28543f37836334b0901d8709275Form = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexef42a28543f37836334b0901d8709275.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
indexef42a28543f37836334b0901d8709275Form.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexef42a28543f37836334b0901d8709275.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
indexef42a28543f37836334b0901d8709275Form.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexef42a28543f37836334b0901d8709275.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexef42a28543f37836334b0901d8709275.form = indexef42a28543f37836334b0901d8709275Form
/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
const index0ccc669a776226eca2e74610abc70805 = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index0ccc669a776226eca2e74610abc70805.url(args, options),
    method: 'get',
})

index0ccc669a776226eca2e74610abc70805.definition = {
    methods: ["get","head"],
    url: '/docs/user/asset/{asset}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
index0ccc669a776226eca2e74610abc70805.url = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { asset: args }
    }

    if (Array.isArray(args)) {
        args = {
            asset: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        asset: args.asset,
    }

    return index0ccc669a776226eca2e74610abc70805.definition.url
            .replace('{asset}', parsedArgs.asset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
index0ccc669a776226eca2e74610abc70805.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index0ccc669a776226eca2e74610abc70805.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
index0ccc669a776226eca2e74610abc70805.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index0ccc669a776226eca2e74610abc70805.url(args, options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
const index0ccc669a776226eca2e74610abc70805Form = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index0ccc669a776226eca2e74610abc70805.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
index0ccc669a776226eca2e74610abc70805Form.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index0ccc669a776226eca2e74610abc70805.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::index
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
index0ccc669a776226eca2e74610abc70805Form.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index0ccc669a776226eca2e74610abc70805.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index0ccc669a776226eca2e74610abc70805.form = index0ccc669a776226eca2e74610abc70805Form

export const index = {
    '/docs/admin/asset/{asset}': indexef42a28543f37836334b0901d8709275,
    '/docs/user/asset/{asset}': index0ccc669a776226eca2e74610abc70805,
}

const SwaggerAssetController = { index }

export default SwaggerAssetController