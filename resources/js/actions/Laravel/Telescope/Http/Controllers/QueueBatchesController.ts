import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::index
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:33
* @route '/telescope/telescope-api/batches'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: index.url(options),
    method: 'post',
})

index.definition = {
    methods: ["post"],
    url: '/telescope/telescope-api/batches',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::index
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:33
* @route '/telescope/telescope-api/batches'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::index
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:33
* @route '/telescope/telescope-api/batches'
*/
index.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: index.url(options),
    method: 'post',
})

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::index
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:33
* @route '/telescope/telescope-api/batches'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: index.url(options),
    method: 'post',
})

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::index
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:33
* @route '/telescope/telescope-api/batches'
*/
indexForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: index.url(options),
    method: 'post',
})

index.form = indexForm

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
export const show = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/telescope/telescope-api/batches/{telescopeEntryId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
show.url = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { telescopeEntryId: args }
    }

    if (Array.isArray(args)) {
        args = {
            telescopeEntryId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        telescopeEntryId: args.telescopeEntryId,
    }

    return show.definition.url
            .replace('{telescopeEntryId}', parsedArgs.telescopeEntryId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
show.get = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
show.head = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
const showForm = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
showForm.get = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Telescope\Http\Controllers\QueueBatchesController::show
* @see vendor/laravel/telescope/src/Http/Controllers/QueueBatchesController.php:41
* @route '/telescope/telescope-api/batches/{telescopeEntryId}'
*/
showForm.head = (args: { telescopeEntryId: string | number } | [telescopeEntryId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const QueueBatchesController = { index, show }

export default QueueBatchesController