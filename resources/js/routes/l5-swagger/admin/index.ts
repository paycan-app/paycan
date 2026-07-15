import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
export const api = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api.url(options),
    method: 'get',
})

api.definition = {
    methods: ["get","head"],
    url: '/api/documentation/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api.url = (options?: RouteQueryOptions) => {
    return api.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: api.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
const apiForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
apiForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
apiForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

api.form = apiForm

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
export const docs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docs.url(options),
    method: 'get',
})

docs.definition = {
    methods: ["get","head"],
    url: '/docs/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs.url = (options?: RouteQueryOptions) => {
    return docs.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docs.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: docs.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
const docsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

docs.form = docsForm

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
export const asset = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: asset.url(args, options),
    method: 'get',
})

asset.definition = {
    methods: ["get","head"],
    url: '/docs/admin/asset/{asset}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
asset.url = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return asset.definition.url
            .replace('{asset}', parsedArgs.asset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
asset.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: asset.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
asset.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: asset.url(args, options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
const assetForm = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: asset.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
assetForm.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: asset.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/admin/asset/{asset}'
*/
assetForm.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: asset.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

asset.form = assetForm

const admin = {
    api: Object.assign(api, api),
    docs: Object.assign(docs, docs),
    asset: Object.assign(asset, asset),
}

export default admin