import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
const StripeSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: StripeSettings.url(options),
    method: 'get',
})

StripeSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/payment-providers/stripe',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
StripeSettings.url = (options?: RouteQueryOptions) => {
    return StripeSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
StripeSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: StripeSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
StripeSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: StripeSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
const StripeSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: StripeSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
StripeSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: StripeSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\StripeSettings::__invoke
* @see app/Filament/Pages/Settings/StripeSettings.php:7
* @route '/admin/settings/payment-providers/stripe'
*/
StripeSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: StripeSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

StripeSettings.form = StripeSettingsForm

export default StripeSettings