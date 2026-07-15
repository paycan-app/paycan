import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
*/
export const demo = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: demo.url(options),
    method: 'get',
})

demo.definition = {
    methods: ["get","head"],
    url: '/checkout-demo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
*/
demo.url = (options?: RouteQueryOptions) => {
    return demo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
*/
demo.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: demo.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
*/
demo.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: demo.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
*/
const demoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: demo.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
*/
demoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: demo.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutPageDemoController::demo
* @see app/Http/Controllers/CheckoutPageDemoController.php:16
* @route '/checkout-demo'
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

const checkout = {
    demo: Object.assign(demo, demo),
}

export default checkout