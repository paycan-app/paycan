import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/portal-demo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PortalDemoController::index
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const PortalDemoController = { index }

export default PortalDemoController