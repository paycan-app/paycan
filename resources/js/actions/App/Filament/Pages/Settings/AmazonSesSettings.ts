import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
const AmazonSesSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AmazonSesSettings.url(options),
    method: 'get',
})

AmazonSesSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/amazon-ses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
AmazonSesSettings.url = (options?: RouteQueryOptions) => {
    return AmazonSesSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
AmazonSesSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AmazonSesSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
AmazonSesSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: AmazonSesSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
const AmazonSesSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AmazonSesSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
AmazonSesSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AmazonSesSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
AmazonSesSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AmazonSesSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

AmazonSesSettings.form = AmazonSesSettingsForm

export default AmazonSesSettings