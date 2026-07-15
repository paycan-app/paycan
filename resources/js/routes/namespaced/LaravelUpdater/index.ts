import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
export const welcome = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: welcome.url(options),
    method: 'get',
})

welcome.definition = {
    methods: ["get","head"],
    url: '/update',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
welcome.url = (options?: RouteQueryOptions) => {
    return welcome.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
welcome.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: welcome.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
welcome.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: welcome.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
const welcomeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: welcome.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
welcomeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: welcome.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::welcome
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:18
* @route '/update'
*/
welcomeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: welcome.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

welcome.form = welcomeForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
export const overview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

overview.definition = {
    methods: ["get","head"],
    url: '/update/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
overview.url = (options?: RouteQueryOptions) => {
    return overview.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
overview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
overview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overview.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
const overviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
overviewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::overview
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:28
* @route '/update/overview'
*/
overviewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

overview.form = overviewForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
export const database = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: database.url(options),
    method: 'get',
})

database.definition = {
    methods: ["get","head"],
    url: '/update/database',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
database.url = (options?: RouteQueryOptions) => {
    return database.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
database.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: database.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
database.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: database.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
const databaseForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: database.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
databaseForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: database.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::database
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:41
* @route '/update/database'
*/
databaseForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: database.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

database.form = databaseForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
export const final = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: final.url(options),
    method: 'get',
})

final.definition = {
    methods: ["get","head"],
    url: '/update/final',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
final.url = (options?: RouteQueryOptions) => {
    return final.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
final.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: final.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
final.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: final.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
const finalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: final.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
finalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: final.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\UpdateController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/UpdateController.php:56
* @route '/update/final'
*/
finalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: final.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

final.form = finalForm

const LaravelUpdater = {
    welcome: Object.assign(welcome, welcome),
    overview: Object.assign(overview, overview),
    database: Object.assign(database, database),
    final: Object.assign(final, final),
}

export default LaravelUpdater