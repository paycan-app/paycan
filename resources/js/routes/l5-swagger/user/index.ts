import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
export const oauth2_callback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oauth2_callback.url(options),
    method: 'get',
})

oauth2_callback.definition = {
    methods: ["get","head"],
    url: '/api/oauth2-callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2_callback.url = (options?: RouteQueryOptions) => {
    return oauth2_callback.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2_callback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oauth2_callback.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2_callback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: oauth2_callback.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
const oauth2_callbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: oauth2_callback.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2_callbackForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: oauth2_callback.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2_callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2_callbackForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: oauth2_callback.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

oauth2_callback.form = oauth2_callbackForm

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
export const api = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api.url(options),
    method: 'get',
})

api.definition = {
    methods: ["get","head"],
    url: '/api/documentation/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
api.url = (options?: RouteQueryOptions) => {
    return api.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
api.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
api.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: api.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
const apiForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
apiForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
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
* @route '/docs/user'
*/
export const docs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docs.url(options),
    method: 'get',
})

docs.definition = {
    methods: ["get","head"],
    url: '/docs/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docs.url = (options?: RouteQueryOptions) => {
    return docs.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docs.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: docs.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
const docsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
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
* @route '/docs/user/asset/{asset}'
*/
export const asset = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: asset.url(args, options),
    method: 'get',
})

asset.definition = {
    methods: ["get","head"],
    url: '/docs/user/asset/{asset}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
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
* @route '/docs/user/asset/{asset}'
*/
asset.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: asset.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
asset.head = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: asset.url(args, options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
const assetForm = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: asset.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
*/
assetForm.get = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: asset.url(args, options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerAssetController::asset
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerAssetController.php:26
* @route '/docs/user/asset/{asset}'
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

const user = {
    oauth2_callback: Object.assign(oauth2_callback, oauth2_callback),
    api: Object.assign(api, api),
    docs: Object.assign(docs, docs),
    asset: Object.assign(asset, asset),
}

export default user