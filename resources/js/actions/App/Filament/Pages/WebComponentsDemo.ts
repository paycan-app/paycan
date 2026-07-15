import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
const WebComponentsDemo = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: WebComponentsDemo.url(options),
    method: 'get',
})

WebComponentsDemo.definition = {
    methods: ["get","head"],
    url: '/admin/web-components-demo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
WebComponentsDemo.url = (options?: RouteQueryOptions) => {
    return WebComponentsDemo.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
WebComponentsDemo.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: WebComponentsDemo.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
WebComponentsDemo.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: WebComponentsDemo.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
const WebComponentsDemoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: WebComponentsDemo.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
WebComponentsDemoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: WebComponentsDemo.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\WebComponentsDemo::__invoke
* @see app/Filament/Pages/WebComponentsDemo.php:7
* @route '/admin/web-components-demo'
*/
WebComponentsDemoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: WebComponentsDemo.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

WebComponentsDemo.form = WebComponentsDemoForm

export default WebComponentsDemo