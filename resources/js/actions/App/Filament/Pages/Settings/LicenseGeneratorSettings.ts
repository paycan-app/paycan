import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
const LicenseGeneratorSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LicenseGeneratorSettings.url(options),
    method: 'get',
})

LicenseGeneratorSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/fulfillment/license-generator',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
LicenseGeneratorSettings.url = (options?: RouteQueryOptions) => {
    return LicenseGeneratorSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
LicenseGeneratorSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LicenseGeneratorSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
LicenseGeneratorSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: LicenseGeneratorSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
const LicenseGeneratorSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LicenseGeneratorSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
LicenseGeneratorSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LicenseGeneratorSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
LicenseGeneratorSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LicenseGeneratorSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

LicenseGeneratorSettings.form = LicenseGeneratorSettingsForm

export default LicenseGeneratorSettings