import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
const MailSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: MailSettings.url(options),
    method: 'get',
})

MailSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
MailSettings.url = (options?: RouteQueryOptions) => {
    return MailSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
MailSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: MailSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
MailSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: MailSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
const MailSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MailSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
MailSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MailSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
MailSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MailSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

MailSettings.form = MailSettingsForm

export default MailSettings