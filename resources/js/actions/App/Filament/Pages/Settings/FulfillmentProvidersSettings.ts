import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
const FulfillmentProvidersSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: FulfillmentProvidersSettings.url(options),
    method: 'get',
})

FulfillmentProvidersSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/fulfillment-providers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
FulfillmentProvidersSettings.url = (options?: RouteQueryOptions) => {
    return FulfillmentProvidersSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
FulfillmentProvidersSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: FulfillmentProvidersSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
FulfillmentProvidersSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: FulfillmentProvidersSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
const FulfillmentProvidersSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FulfillmentProvidersSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
FulfillmentProvidersSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FulfillmentProvidersSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
FulfillmentProvidersSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FulfillmentProvidersSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

FulfillmentProvidersSettings.form = FulfillmentProvidersSettingsForm

export default FulfillmentProvidersSettings