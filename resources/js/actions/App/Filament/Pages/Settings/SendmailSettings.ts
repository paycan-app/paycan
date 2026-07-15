import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
const SendmailSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SendmailSettings.url(options),
    method: 'get',
})

SendmailSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/sendmail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
SendmailSettings.url = (options?: RouteQueryOptions) => {
    return SendmailSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
SendmailSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SendmailSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
SendmailSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: SendmailSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
const SendmailSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: SendmailSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
SendmailSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: SendmailSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
SendmailSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: SendmailSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

SendmailSettings.form = SendmailSettingsForm

export default SendmailSettings