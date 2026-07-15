import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ListTransactions::__invoke
* @see app/Filament/Resources/Transactions/Pages/ListTransactions.php:7
* @route '/admin/transactions'
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

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
export const view = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
view.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
view.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
view.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
const viewForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
viewForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\ViewTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/ViewTransaction.php:7
* @route '/admin/transactions/{record}'
*/
viewForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

view.form = viewForm

const transactions = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    view: Object.assign(view, view),
}

export default transactions