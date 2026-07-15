import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
const ListSubscriptions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListSubscriptions.url(options),
    method: 'get',
})

ListSubscriptions.definition = {
    methods: ["get","head"],
    url: '/admin/subscriptions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
ListSubscriptions.url = (options?: RouteQueryOptions) => {
    return ListSubscriptions.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
ListSubscriptions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListSubscriptions.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
ListSubscriptions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListSubscriptions.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
const ListSubscriptionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListSubscriptions.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
ListSubscriptionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListSubscriptions.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
ListSubscriptionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListSubscriptions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListSubscriptions.form = ListSubscriptionsForm

export default ListSubscriptions