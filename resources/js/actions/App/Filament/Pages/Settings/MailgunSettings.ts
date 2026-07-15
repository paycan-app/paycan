import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
const MailgunSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: MailgunSettings.url(options),
    method: 'get',
})

MailgunSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/mailgun',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
MailgunSettings.url = (options?: RouteQueryOptions) => {
    return MailgunSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
MailgunSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: MailgunSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
MailgunSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: MailgunSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
const MailgunSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MailgunSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
MailgunSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MailgunSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
MailgunSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MailgunSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

MailgunSettings.form = MailgunSettingsForm

export default MailgunSettings