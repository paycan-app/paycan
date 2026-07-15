import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
const api0aa87d0a97519dc5e72fe01dda471c34 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api0aa87d0a97519dc5e72fe01dda471c34.url(options),
    method: 'get',
})

api0aa87d0a97519dc5e72fe01dda471c34.definition = {
    methods: ["get","head"],
    url: '/api/documentation/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api0aa87d0a97519dc5e72fe01dda471c34.url = (options?: RouteQueryOptions) => {
    return api0aa87d0a97519dc5e72fe01dda471c34.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api0aa87d0a97519dc5e72fe01dda471c34.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api0aa87d0a97519dc5e72fe01dda471c34.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api0aa87d0a97519dc5e72fe01dda471c34.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: api0aa87d0a97519dc5e72fe01dda471c34.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
const api0aa87d0a97519dc5e72fe01dda471c34Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api0aa87d0a97519dc5e72fe01dda471c34.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api0aa87d0a97519dc5e72fe01dda471c34Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api0aa87d0a97519dc5e72fe01dda471c34.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/admin'
*/
api0aa87d0a97519dc5e72fe01dda471c34Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: api0aa87d0a97519dc5e72fe01dda471c34.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

api0aa87d0a97519dc5e72fe01dda471c34.form = api0aa87d0a97519dc5e72fe01dda471c34Form
/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
const apidf403eb2e4d4c5779d0fadd50a5bb63f = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: apidf403eb2e4d4c5779d0fadd50a5bb63f.url(options),
    method: 'get',
})

apidf403eb2e4d4c5779d0fadd50a5bb63f.definition = {
    methods: ["get","head"],
    url: '/api/documentation/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
apidf403eb2e4d4c5779d0fadd50a5bb63f.url = (options?: RouteQueryOptions) => {
    return apidf403eb2e4d4c5779d0fadd50a5bb63f.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
apidf403eb2e4d4c5779d0fadd50a5bb63f.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: apidf403eb2e4d4c5779d0fadd50a5bb63f.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
apidf403eb2e4d4c5779d0fadd50a5bb63f.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: apidf403eb2e4d4c5779d0fadd50a5bb63f.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
const apidf403eb2e4d4c5779d0fadd50a5bb63fForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: apidf403eb2e4d4c5779d0fadd50a5bb63f.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
apidf403eb2e4d4c5779d0fadd50a5bb63fForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: apidf403eb2e4d4c5779d0fadd50a5bb63f.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::api
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:92
* @route '/api/documentation/user'
*/
apidf403eb2e4d4c5779d0fadd50a5bb63fForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: apidf403eb2e4d4c5779d0fadd50a5bb63f.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

apidf403eb2e4d4c5779d0fadd50a5bb63f.form = apidf403eb2e4d4c5779d0fadd50a5bb63fForm

export const api = {
    '/api/documentation/admin': api0aa87d0a97519dc5e72fe01dda471c34,
    '/api/documentation/user': apidf403eb2e4d4c5779d0fadd50a5bb63f,
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
const docs13779215743081fad4528e06c2c680e2 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docs13779215743081fad4528e06c2c680e2.url(options),
    method: 'get',
})

docs13779215743081fad4528e06c2c680e2.definition = {
    methods: ["get","head"],
    url: '/docs/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs13779215743081fad4528e06c2c680e2.url = (options?: RouteQueryOptions) => {
    return docs13779215743081fad4528e06c2c680e2.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs13779215743081fad4528e06c2c680e2.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docs13779215743081fad4528e06c2c680e2.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs13779215743081fad4528e06c2c680e2.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: docs13779215743081fad4528e06c2c680e2.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
const docs13779215743081fad4528e06c2c680e2Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs13779215743081fad4528e06c2c680e2.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs13779215743081fad4528e06c2c680e2Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs13779215743081fad4528e06c2c680e2.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/admin'
*/
docs13779215743081fad4528e06c2c680e2Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docs13779215743081fad4528e06c2c680e2.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

docs13779215743081fad4528e06c2c680e2.form = docs13779215743081fad4528e06c2c680e2Form
/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
const docsd1177bf25f87a2ba86412addda91325e = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docsd1177bf25f87a2ba86412addda91325e.url(options),
    method: 'get',
})

docsd1177bf25f87a2ba86412addda91325e.definition = {
    methods: ["get","head"],
    url: '/docs/user',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docsd1177bf25f87a2ba86412addda91325e.url = (options?: RouteQueryOptions) => {
    return docsd1177bf25f87a2ba86412addda91325e.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docsd1177bf25f87a2ba86412addda91325e.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: docsd1177bf25f87a2ba86412addda91325e.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docsd1177bf25f87a2ba86412addda91325e.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: docsd1177bf25f87a2ba86412addda91325e.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
const docsd1177bf25f87a2ba86412addda91325eForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docsd1177bf25f87a2ba86412addda91325e.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docsd1177bf25f87a2ba86412addda91325eForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docsd1177bf25f87a2ba86412addda91325e.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::docs
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:34
* @route '/docs/user'
*/
docsd1177bf25f87a2ba86412addda91325eForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: docsd1177bf25f87a2ba86412addda91325e.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

docsd1177bf25f87a2ba86412addda91325e.form = docsd1177bf25f87a2ba86412addda91325eForm

export const docs = {
    '/docs/admin': docs13779215743081fad4528e06c2c680e2,
    '/docs/user': docsd1177bf25f87a2ba86412addda91325e,
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
export const oauth2Callback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oauth2Callback.url(options),
    method: 'get',
})

oauth2Callback.definition = {
    methods: ["get","head"],
    url: '/api/oauth2-callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2Callback.url = (options?: RouteQueryOptions) => {
    return oauth2Callback.definition.url + queryParams(options)
}

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2Callback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oauth2Callback.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2Callback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: oauth2Callback.url(options),
    method: 'head',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
const oauth2CallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: oauth2Callback.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2CallbackForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: oauth2Callback.url(options),
    method: 'get',
})

/**
* @see \L5Swagger\Http\Controllers\SwaggerController::oauth2Callback
* @see vendor/darkaonline/l5-swagger/src/Http/Controllers/SwaggerController.php:142
* @route '/api/oauth2-callback'
*/
oauth2CallbackForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: oauth2Callback.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

oauth2Callback.form = oauth2CallbackForm

const SwaggerController = { api, docs, oauth2Callback }

export default SwaggerController