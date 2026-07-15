import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
const CreateTransaction = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateTransaction.url(options),
    method: 'get',
})

CreateTransaction.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
CreateTransaction.url = (options?: RouteQueryOptions) => {
    return CreateTransaction.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
CreateTransaction.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateTransaction.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
CreateTransaction.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateTransaction.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
const CreateTransactionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateTransaction.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
CreateTransactionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateTransaction.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Transactions\Pages\CreateTransaction::__invoke
* @see app/Filament/Resources/Transactions/Pages/CreateTransaction.php:7
* @route '/admin/transactions/create'
*/
CreateTransactionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateTransaction.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CreateTransaction.form = CreateTransactionForm

export default CreateTransaction