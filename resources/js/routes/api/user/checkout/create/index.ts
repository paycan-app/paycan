import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\User\CheckoutController::guest
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
export const guest = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: guest.url(options),
    method: 'post',
})

guest.definition = {
    methods: ["post"],
    url: '/api/user/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::guest
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
guest.url = (options?: RouteQueryOptions) => {
    return guest.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::guest
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
guest.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: guest.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::guest
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
const guestForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: guest.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\CheckoutController::guest
* @see app/Http/Controllers/Api/User/CheckoutController.php:99
* @route '/api/user/checkout'
*/
guestForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: guest.url(options),
    method: 'post',
})

guest.form = guestForm

const create = {
    guest: Object.assign(guest, guest),
}

export default create