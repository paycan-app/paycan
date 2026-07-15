import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/install/admin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const admin = {
    store: Object.assign(store, store),
}

export default admin