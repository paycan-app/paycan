import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
const NotificationSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: NotificationSettings.url(options),
    method: 'get',
})

NotificationSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
NotificationSettings.url = (options?: RouteQueryOptions) => {
    return NotificationSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
NotificationSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: NotificationSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
NotificationSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: NotificationSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
const NotificationSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: NotificationSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
NotificationSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: NotificationSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
NotificationSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: NotificationSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

NotificationSettings.form = NotificationSettingsForm

export default NotificationSettings