import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
const ListTransactions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListTransactions.url(options),
    method: 'get',
})

ListTransactions.definition = {
    methods: ["get","head"],
    url: '/admin/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
ListTransactions.url = (options?: RouteQueryOptions) => {
    return ListTransactions.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
ListTransactions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListTransactions.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
ListTransactions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListTransactions.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
const ListTransactionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListTransactions.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
ListTransactionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListTransactions.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
ListTransactionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListTransactions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListTransactions.form = ListTransactionsForm

export default ListTransactions