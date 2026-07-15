import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::generateToken
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
export const generateToken = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateToken.url(options),
    method: 'post',
})

generateToken.definition = {
    methods: ["post"],
    url: '/api/admin/users/sync',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::generateToken
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
generateToken.url = (options?: RouteQueryOptions) => {
    return generateToken.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::generateToken
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
generateToken.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateToken.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::generateToken
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
const generateTokenForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateToken.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Admin\UserTokenController::generateToken
* @see app/Http/Controllers/Api/Admin/UserTokenController.php:60
* @route '/api/admin/users/sync'
*/
generateTokenForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateToken.url(options),
    method: 'post',
})

generateToken.form = generateTokenForm

const UserTokenController = { generateToken }

export default UserTokenController