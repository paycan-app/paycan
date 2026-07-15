import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
const GoogleSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GoogleSettings.url(options),
    method: 'get',
})

GoogleSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/social-login/google',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
GoogleSettings.url = (options?: RouteQueryOptions) => {
    return GoogleSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
GoogleSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GoogleSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
GoogleSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: GoogleSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
const GoogleSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GoogleSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
GoogleSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GoogleSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
GoogleSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GoogleSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

GoogleSettings.form = GoogleSettingsForm

export default GoogleSettings