import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::sync
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
export const sync = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(options),
    method: 'post',
})

sync.definition = {
    methods: ["post"],
    url: '/api/admin/users/sync',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::sync
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
sync.url = (options?: RouteQueryOptions) => {
    return sync.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::sync
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
sync.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::sync
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
const syncForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::sync
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
syncForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(options),
    method: 'post',
})

sync.form = syncForm

const users = {
    sync: Object.assign(sync, sync),
}

export default users