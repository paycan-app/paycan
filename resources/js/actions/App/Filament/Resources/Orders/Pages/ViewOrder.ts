import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
const ViewOrder = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewOrder.url(args, options),
    method: 'get',
})

ViewOrder.definition = {
    methods: ["get","head"],
    url: '/admin/orders/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
ViewOrder.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return ViewOrder.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
ViewOrder.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewOrder.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
ViewOrder.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ViewOrder.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
const ViewOrderForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewOrder.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
ViewOrderForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewOrder.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Orders\Pages\ViewOrder::__invoke
* @see app/Filament/Resources/Orders/Pages/ViewOrder.php:7
* @route '/admin/orders/{record}'
*/
ViewOrderForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewOrder.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ViewOrder.form = ViewOrderForm

export default ViewOrder