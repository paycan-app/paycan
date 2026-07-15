import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Telescope\Http\Controllers\EntriesController::destroy
* @see vendor/laravel/telescope/src/Http/Controllers/EntriesController.php:16
* @route '/telescope/telescope-api/entries'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/telescope/telescope-api/entries',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Laravel\Telescope\Http\Controllers\EntriesController::destroy
* @see vendor/laravel/telescope/src/Http/Controllers/EntriesController.php:16
* @route '/telescope/telescope-api/entries'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \Laravel\Telescope\Http\Controllers\EntriesController::destroy
* @see vendor/laravel/telescope/src/Http/Controllers/EntriesController.php:16
* @route '/telescope/telescope-api/entries'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \Laravel\Telescope\Http\Controllers\EntriesController::destroy
* @see vendor/laravel/telescope/src/Http/Controllers/EntriesController.php:16
* @route '/telescope/telescope-api/entries'
*/
const destroyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Laravel\Telescope\Http\Controllers\EntriesController::destroy
* @see vendor/laravel/telescope/src/Http/Controllers/EntriesController.php:16
* @route '/telescope/telescope-api/entries'
*/
destroyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const EntriesController = { destroy }

export default EntriesController