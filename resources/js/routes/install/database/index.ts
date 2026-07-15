import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\InstallController::test
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
export const test = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: test.url(options),
    method: 'post',
})

test.definition = {
    methods: ["post"],
    url: '/install/database/test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InstallController::test
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
test.url = (options?: RouteQueryOptions) => {
    return test.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::test
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
test.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: test.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::test
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
const testForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: test.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::test
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
testForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: test.url(options),
    method: 'post',
})

test.form = testForm

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/install/database',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::store
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const database = {
    test: Object.assign(test, test),
    store: Object.assign(store, store),
}

export default database