import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
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
const me4c0ab272af8dddb89200ddf10d5127f2 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me4c0ab272af8dddb89200ddf10d5127f2.url(options),
    method: 'get',
})

me4c0ab272af8dddb89200ddf10d5127f2.definition = {
    methods: ["get","head"],
    url: '/api/auth/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me4c0ab272af8dddb89200ddf10d5127f2.url = (options?: RouteQueryOptions) => {
    return me4c0ab272af8dddb89200ddf10d5127f2.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me4c0ab272af8dddb89200ddf10d5127f2.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me4c0ab272af8dddb89200ddf10d5127f2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me4c0ab272af8dddb89200ddf10d5127f2.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me4c0ab272af8dddb89200ddf10d5127f2.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
const me4c0ab272af8dddb89200ddf10d5127f2Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me4c0ab272af8dddb89200ddf10d5127f2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me4c0ab272af8dddb89200ddf10d5127f2Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me4c0ab272af8dddb89200ddf10d5127f2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/auth/me'
*/
me4c0ab272af8dddb89200ddf10d5127f2Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me4c0ab272af8dddb89200ddf10d5127f2.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

me4c0ab272af8dddb89200ddf10d5127f2.form = me4c0ab272af8dddb89200ddf10d5127f2Form
/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
const mefca88b55e80f8c315e3f552edfba1fb8 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mefca88b55e80f8c315e3f552edfba1fb8.url(options),
    method: 'get',
})

mefca88b55e80f8c315e3f552edfba1fb8.definition = {
    methods: ["get","head"],
    url: '/api/user/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
mefca88b55e80f8c315e3f552edfba1fb8.url = (options?: RouteQueryOptions) => {
    return mefca88b55e80f8c315e3f552edfba1fb8.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
mefca88b55e80f8c315e3f552edfba1fb8.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mefca88b55e80f8c315e3f552edfba1fb8.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
mefca88b55e80f8c315e3f552edfba1fb8.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mefca88b55e80f8c315e3f552edfba1fb8.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
const mefca88b55e80f8c315e3f552edfba1fb8Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mefca88b55e80f8c315e3f552edfba1fb8.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
mefca88b55e80f8c315e3f552edfba1fb8Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mefca88b55e80f8c315e3f552edfba1fb8.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
mefca88b55e80f8c315e3f552edfba1fb8Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mefca88b55e80f8c315e3f552edfba1fb8.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mefca88b55e80f8c315e3f552edfba1fb8.form = mefca88b55e80f8c315e3f552edfba1fb8Form

export const me = {
    '/api/auth/me': me4c0ab272af8dddb89200ddf10d5127f2,
    '/api/user/me': mefca88b55e80f8c315e3f552edfba1fb8,
}

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

const AuthController = { logout, me, refresh }

export default AuthController