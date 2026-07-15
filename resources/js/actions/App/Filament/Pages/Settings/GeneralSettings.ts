import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
const GeneralSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GeneralSettings.url(options),
    method: 'get',
})

GeneralSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/general',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
GeneralSettings.url = (options?: RouteQueryOptions) => {
    return GeneralSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
GeneralSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GeneralSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
GeneralSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: GeneralSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
const GeneralSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GeneralSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
GeneralSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GeneralSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
GeneralSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GeneralSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

GeneralSettings.form = GeneralSettingsForm

export default GeneralSettings