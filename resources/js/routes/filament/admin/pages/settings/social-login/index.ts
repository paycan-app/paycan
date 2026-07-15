import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
export const facebook = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: facebook.url(options),
    method: 'get',
})

facebook.definition = {
    methods: ["get","head"],
    url: '/admin/settings/social-login/facebook',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
facebook.url = (options?: RouteQueryOptions) => {
    return facebook.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
facebook.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: facebook.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
facebook.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: facebook.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
const facebookForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: facebook.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
facebookForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: facebook.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FacebookSettings::__invoke
* @see app/Filament/Pages/Settings/FacebookSettings.php:7
* @route '/admin/settings/social-login/facebook'
*/
facebookForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: facebook.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

facebook.form = facebookForm

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
export const github = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: github.url(options),
    method: 'get',
})

github.definition = {
    methods: ["get","head"],
    url: '/admin/settings/social-login/github',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
github.url = (options?: RouteQueryOptions) => {
    return github.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
github.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: github.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
github.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: github.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
const githubForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: github.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
githubForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: github.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
githubForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: github.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

github.form = githubForm

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
export const google = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: google.url(options),
    method: 'get',
})

google.definition = {
    methods: ["get","head"],
    url: '/admin/settings/social-login/google',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
google.url = (options?: RouteQueryOptions) => {
    return google.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
google.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: google.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
google.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: google.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
const googleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: google.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
googleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: google.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GoogleSettings::__invoke
* @see app/Filament/Pages/Settings/GoogleSettings.php:7
* @route '/admin/settings/social-login/google'
*/
googleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: google.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

google.form = googleForm

const socialLogin = {
    facebook: Object.assign(facebook, facebook),
    github: Object.assign(github, github),
    google: Object.assign(google, google),
}

export default socialLogin