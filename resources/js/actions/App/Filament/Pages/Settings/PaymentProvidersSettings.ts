import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
const PaymentProvidersSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PaymentProvidersSettings.url(options),
    method: 'get',
})

PaymentProvidersSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/payment-providers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
PaymentProvidersSettings.url = (options?: RouteQueryOptions) => {
    return PaymentProvidersSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
PaymentProvidersSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PaymentProvidersSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
PaymentProvidersSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: PaymentProvidersSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
const PaymentProvidersSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PaymentProvidersSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
PaymentProvidersSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PaymentProvidersSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
PaymentProvidersSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PaymentProvidersSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

PaymentProvidersSettings.form = PaymentProvidersSettingsForm

export default PaymentProvidersSettings