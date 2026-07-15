import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
const Login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Login.url(options),
    method: 'get',
})

Login.definition = {
    methods: ["get","head"],
    url: '/admin/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
Login.url = (options?: RouteQueryOptions) => {
    return Login.definition.url + queryParams(options)
}

/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
Login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Login.url(options),
    method: 'get',
})

/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
Login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Login.url(options),
    method: 'head',
})

/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
const LoginForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Login.url(options),
    method: 'get',
})

/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
LoginForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Login.url(options),
    method: 'get',
})

/**
* @see \Filament\Auth\Pages\Login::__invoke
* @see vendor/filament/filament/src/Auth/Pages/Login.php:7
* @route '/admin/login'
*/
LoginForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Login.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Login.form = LoginForm

export default Login