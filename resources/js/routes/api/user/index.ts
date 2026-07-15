import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import orders from './orders'
import subscriptions from './subscriptions'
import checkout from './checkout'
import transactions from './transactions'
import products from './products'
/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
export const me = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

me.definition = {
    methods: ["get","head"],
    url: '/api/user/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
me.url = (options?: RouteQueryOptions) => {
    return me.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
me.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
me.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
const meForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
meForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\AuthController::me
* @see app/Http/Controllers/Api/User/AuthController.php:232
* @route '/api/user/me'
*/
meForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

me.form = meForm

const user = {
    me: Object.assign(me, me),
    orders: Object.assign(orders, orders),
    subscriptions: Object.assign(subscriptions, subscriptions),
    checkout: Object.assign(checkout, checkout),
    transactions: Object.assign(transactions, transactions),
    products: Object.assign(products, products),
}

export default user