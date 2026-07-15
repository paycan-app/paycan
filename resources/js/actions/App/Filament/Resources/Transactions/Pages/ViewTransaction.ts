import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
const ViewTransaction = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewTransaction.url(args, options),
    method: 'get',
})

ViewTransaction.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
ViewTransaction.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ViewTransaction.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
ViewTransaction.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewTransaction.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
ViewTransaction.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ViewTransaction.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
const ViewTransactionForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewTransaction.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
ViewTransactionForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewTransaction.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
ViewTransactionForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewTransaction.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ViewTransaction.form = ViewTransactionForm

export default ViewTransaction