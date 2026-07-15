import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\User\AuthController::logout
* @see app/Http/Controllers/Api/User/AuthController.php:58
* @route '/api/auth/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/api/auth/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\AuthController::logout
* @see app/Http/Controllers/Api/User/AuthController.php:58
* @route '/api/auth/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\AuthController::logout
* @see app/Http/Controllers/Api/User/AuthController.php:58
* @route '/api/auth/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::logout
* @see app/Http/Controllers/Api/User/AuthController.php:58
* @route '/api/auth/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::logout
* @see app/Http/Controllers/Api/User/AuthController.php:58
* @route '/api/auth/logout'
*/
logoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

logout.form = logoutForm

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
export const me = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

me.definition = {
    methods: ["get","head"],
    url: '/api/auth/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me.url = (options?: RouteQueryOptions) => {
    return me.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
const meForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
meForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
meForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

me.form = meForm

/**
* @see \App\Http\Controllers\Api\User\AuthController::refresh
* @see app/Http/Controllers/Api/User/AuthController.php:268
* @route '/api/auth/refresh'
*/
export const refresh = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refresh.url(options),
    method: 'post',
})

refresh.definition = {
    methods: ["post"],
    url: '/api/auth/refresh',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\AuthController::refresh
* @see app/Http/Controllers/Api/User/AuthController.php:268
* @route '/api/auth/refresh'
*/
refresh.url = (options?: RouteQueryOptions) => {
    return refresh.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\AuthController::refresh
* @see app/Http/Controllers/Api/User/AuthController.php:268
* @route '/api/auth/refresh'
*/
refresh.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refresh.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::refresh
* @see app/Http/Controllers/Api/User/AuthController.php:268
* @route '/api/auth/refresh'
*/
const refreshForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refresh.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::refresh
* @see app/Http/Controllers/Api/User/AuthController.php:268
* @route '/api/auth/refresh'
*/
refreshForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refresh.url(options),
    method: 'post',
})

refresh.form = refreshForm

const auth = {
    logout: Object.assign(logout, logout),
    me: Object.assign(me, me),
    refresh: Object.assign(refresh, refresh),
}

export default auth