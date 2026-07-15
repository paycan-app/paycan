import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
const PostmarkSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PostmarkSettings.url(options),
    method: 'get',
})

PostmarkSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/postmark',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
PostmarkSettings.url = (options?: RouteQueryOptions) => {
    return PostmarkSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
PostmarkSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PostmarkSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
PostmarkSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: PostmarkSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
const PostmarkSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PostmarkSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
PostmarkSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PostmarkSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
PostmarkSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PostmarkSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

PostmarkSettings.form = PostmarkSettingsForm

export default PostmarkSettings