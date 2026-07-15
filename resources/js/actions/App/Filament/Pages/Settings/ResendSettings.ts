import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
const ResendSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ResendSettings.url(options),
    method: 'get',
})

ResendSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/resend',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
ResendSettings.url = (options?: RouteQueryOptions) => {
    return ResendSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
ResendSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ResendSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
ResendSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ResendSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
const ResendSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ResendSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
ResendSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ResendSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
ResendSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ResendSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ResendSettings.form = ResendSettingsForm

export default ResendSettings