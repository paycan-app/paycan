import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
export const permissions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: permissions.url(options),
    method: 'get',
})

permissions.definition = {
    methods: ["get","head"],
    url: '/install/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissions.url = (options?: RouteQueryOptions) => {
    return permissions.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: permissions.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: permissions.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
const permissionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: permissions.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: permissions.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: permissions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

permissions.form = permissionsForm

const PermissionsController = { permissions }

export default PermissionsController