import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
const PayPalSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PayPalSettings.url(options),
    method: 'get',
})

PayPalSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/payment-providers/paypal',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
PayPalSettings.url = (options?: RouteQueryOptions) => {
    return PayPalSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
PayPalSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PayPalSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
PayPalSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: PayPalSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
const PayPalSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PayPalSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
PayPalSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PayPalSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PayPalSettings::__invoke
* @see app/Filament/Pages/Settings/PayPalSettings.php:7
* @route '/admin/settings/payment-providers/paypal'
*/
PayPalSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PayPalSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

PayPalSettings.form = PayPalSettingsForm

export default PayPalSettings