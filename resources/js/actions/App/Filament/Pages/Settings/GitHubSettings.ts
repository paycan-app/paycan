import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
const GitHubSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GitHubSettings.url(options),
    method: 'get',
})

GitHubSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/social-login/github',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
GitHubSettings.url = (options?: RouteQueryOptions) => {
    return GitHubSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
GitHubSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: GitHubSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
GitHubSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: GitHubSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
const GitHubSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GitHubSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
GitHubSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GitHubSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GitHubSettings::__invoke
* @see app/Filament/Pages/Settings/GitHubSettings.php:7
* @route '/admin/settings/social-login/github'
*/
GitHubSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: GitHubSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

GitHubSettings.form = GitHubSettingsForm

export default GitHubSettings