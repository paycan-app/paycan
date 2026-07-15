import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
import settings from './settings'
/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \Filament\Pages\Dashboard::__invoke
* @see vendor/filament/filament/src/Pages/Dashboard.php:7
* @route '/admin'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
export const webComponentsDemo = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webComponentsDemo.url(options),
    method: 'get',
})

webComponentsDemo.definition = {
    methods: ["get","head"],
    url: '/admin/web-components-demo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
webComponentsDemo.url = (options?: RouteQueryOptions) => {
    return webComponentsDemo.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
webComponentsDemo.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webComponentsDemo.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
webComponentsDemo.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: webComponentsDemo.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
const webComponentsDemoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: webComponentsDemo.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
webComponentsDemoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: webComponentsDemo.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
webComponentsDemoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: webComponentsDemo.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

webComponentsDemo.form = webComponentsDemoForm

const pages = {
    dashboard: Object.assign(dashboard, dashboard),
    settings: Object.assign(settings, settings),
    webComponentsDemo: Object.assign(webComponentsDemo, webComponentsDemo),
}

export default pages