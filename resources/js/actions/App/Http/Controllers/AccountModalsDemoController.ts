import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/account-modals-demo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountModalsDemoController::index
* @see app/Http/Controllers/AccountModalsDemoController.php:19
* @route '/account-modals-demo'
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

const AccountModalsDemoController = { index }

export default AccountModalsDemoController