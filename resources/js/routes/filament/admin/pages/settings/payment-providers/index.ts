import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
export const paypal = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paypal.url(options),
    method: 'get',
})

paypal.definition = {
    methods: ["get","head"],
    url: '/admin/settings/payment-providers/paypal',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
paypal.url = (options?: RouteQueryOptions) => {
    return paypal.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
paypal.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paypal.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
paypal.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: paypal.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
const paypalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paypal.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
paypalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paypal.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
paypalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paypal.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

paypal.form = paypalForm

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
export const stripe = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stripe.url(options),
    method: 'get',
})

stripe.definition = {
    methods: ["get","head"],
    url: '/admin/settings/payment-providers/stripe',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
stripe.url = (options?: RouteQueryOptions) => {
    return stripe.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
stripe.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stripe.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
stripe.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stripe.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
const stripeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stripe.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
stripeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stripe.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
stripeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stripe.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

stripe.form = stripeForm

const paymentProviders = {
    stripe: Object.assign(stripe, stripe),
}

export default paymentProviders