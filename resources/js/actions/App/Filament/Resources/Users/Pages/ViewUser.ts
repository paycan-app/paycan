import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
const ViewUser = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewUser.url(args, options),
    method: 'get',
})

ViewUser.definition = {
    methods: ["get","head"],
    url: '/admin/users/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
ViewUser.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return ViewUser.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
ViewUser.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewUser.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
ViewUser.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ViewUser.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
const ViewUserForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewUser.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
ViewUserForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewUser.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Users\Pages\ViewUser::__invoke
* @see app/Filament/Resources/Users/Pages/ViewUser.php:7
* @route '/admin/users/{record}'
*/
ViewUserForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ViewUser.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ViewUser.form = ViewUserForm

export default ViewUser