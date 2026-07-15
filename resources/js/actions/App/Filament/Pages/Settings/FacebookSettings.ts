import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
const FacebookSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: FacebookSettings.url(options),
    method: 'get',
})

FacebookSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/social-login/facebook',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
FacebookSettings.url = (options?: RouteQueryOptions) => {
    return FacebookSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
FacebookSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: FacebookSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
FacebookSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: FacebookSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
const FacebookSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FacebookSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
FacebookSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FacebookSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
FacebookSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FacebookSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

FacebookSettings.form = FacebookSettingsForm

export default FacebookSettings