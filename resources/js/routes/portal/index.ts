import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
export const demo = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: demo.url(options),
    method: 'get',
})

demo.definition = {
    methods: ["get","head"],
    url: '/portal-demo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
demo.url = (options?: RouteQueryOptions) => {
    return demo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
demo.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: demo.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
demo.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: demo.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
const demoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: demo.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
demoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: demo.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PortalDemoController::demo
* @see app/Http/Controllers/PortalDemoController.php:16
* @route '/portal-demo'
*/
demoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: demo.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

demo.form = demoForm

const portal = {
    demo: Object.assign(demo, demo),
}

export default portal