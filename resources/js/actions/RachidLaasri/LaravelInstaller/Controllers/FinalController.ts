import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
export const finish = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: finish.url(options),
    method: 'get',
})

finish.definition = {
    methods: ["get","head"],
    url: '/install/final',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
finish.url = (options?: RouteQueryOptions) => {
    return finish.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
finish.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: finish.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
finish.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: finish.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
const finishForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finish.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
finishForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finish.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::finish
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
finishForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finish.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

finish.form = finishForm

const FinalController = { finish }

export default FinalController