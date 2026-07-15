import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
const EditProfile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditProfile.url(options),
    method: 'get',
})

EditProfile.definition = {
    methods: ["get","head"],
    url: '/admin/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
EditProfile.url = (options?: RouteQueryOptions) => {
    return EditProfile.definition.url + queryParams(options)
}

/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
EditProfile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditProfile.url(options),
    method: 'get',
})

/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
EditProfile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditProfile.url(options),
    method: 'head',
})

/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
const EditProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditProfile.url(options),
    method: 'get',
})

/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
EditProfileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditProfile.url(options),
    method: 'get',
})

/**
* @see \Filament\Auth\Pages\EditProfile::__invoke
* @see vendor/filament/filament/src/Auth/Pages/EditProfile.php:7
* @route '/admin/profile'
*/
EditProfileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditProfile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EditProfile.form = EditProfileForm

export default EditProfile