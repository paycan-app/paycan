import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
*/
export const welcome = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: welcome.url(options),
    method: 'get',
})

welcome.definition = {
    methods: ["get","head"],
    url: '/install',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
*/
welcome.url = (options?: RouteQueryOptions) => {
    return welcome.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
*/
welcome.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: welcome.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
*/
welcome.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: welcome.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
*/
const welcomeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: welcome.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
*/
welcomeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: welcome.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::welcome
* @see app/Http/Controllers/InstallController.php:16
* @route '/install'
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
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
export const requirements = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: requirements.url(options),
    method: 'get',
})

requirements.definition = {
    methods: ["get","head"],
    url: '/install/requirements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
requirements.url = (options?: RouteQueryOptions) => {
    return requirements.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
requirements.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: requirements.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
requirements.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: requirements.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
const requirementsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: requirements.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
requirementsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: requirements.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::requirements
* @see app/Http/Controllers/InstallController.php:25
* @route '/install/requirements'
*/
requirementsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: requirements.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

requirements.form = requirementsForm

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
*/
export const database = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: database.url(options),
    method: 'get',
})

database.definition = {
    methods: ["get","head"],
    url: '/install/database',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
*/
database.url = (options?: RouteQueryOptions) => {
    return database.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
*/
database.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: database.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
*/
database.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: database.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
*/
const databaseForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: database.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
*/
databaseForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: database.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::database
* @see app/Http/Controllers/InstallController.php:36
* @route '/install/database'
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
* @see \App\Http\Controllers\InstallController::testDatabase
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
export const testDatabase = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testDatabase.url(options),
    method: 'post',
})

testDatabase.definition = {
    methods: ["post"],
    url: '/install/database/test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InstallController::testDatabase
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
testDatabase.url = (options?: RouteQueryOptions) => {
    return testDatabase.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::testDatabase
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
testDatabase.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testDatabase.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::testDatabase
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
const testDatabaseForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testDatabase.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::testDatabase
* @see app/Http/Controllers/InstallController.php:45
* @route '/install/database/test'
*/
testDatabaseForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testDatabase.url(options),
    method: 'post',
})

testDatabase.form = testDatabaseForm

/**
* @see \App\Http\Controllers\InstallController::databaseStore
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
export const databaseStore = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: databaseStore.url(options),
    method: 'post',
})

databaseStore.definition = {
    methods: ["post"],
    url: '/install/database',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InstallController::databaseStore
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
databaseStore.url = (options?: RouteQueryOptions) => {
    return databaseStore.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::databaseStore
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
databaseStore.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: databaseStore.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::databaseStore
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
const databaseStoreForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: databaseStore.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::databaseStore
* @see app/Http/Controllers/InstallController.php:111
* @route '/install/database'
*/
databaseStoreForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: databaseStore.url(options),
    method: 'post',
})

databaseStore.form = databaseStoreForm

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
export const admin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

admin.definition = {
    methods: ["get","head"],
    url: '/install/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
admin.url = (options?: RouteQueryOptions) => {
    return admin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
admin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
admin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: admin.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
const adminForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
adminForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::admin
* @see app/Http/Controllers/InstallController.php:152
* @route '/install/admin'
*/
adminForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

admin.form = adminForm

/**
* @see \App\Http\Controllers\InstallController::adminStore
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
export const adminStore = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: adminStore.url(options),
    method: 'post',
})

adminStore.definition = {
    methods: ["post"],
    url: '/install/admin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InstallController::adminStore
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
adminStore.url = (options?: RouteQueryOptions) => {
    return adminStore.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::adminStore
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
adminStore.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: adminStore.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::adminStore
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
const adminStoreForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: adminStore.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InstallController::adminStore
* @see app/Http/Controllers/InstallController.php:161
* @route '/install/admin'
*/
adminStoreForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: adminStore.url(options),
    method: 'post',
})

adminStore.form = adminStoreForm

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
export const complete = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complete.url(options),
    method: 'get',
})

complete.definition = {
    methods: ["get","head"],
    url: '/install/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
complete.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complete.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
complete.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complete.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
const completeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complete.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
completeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complete.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InstallController::complete
* @see app/Http/Controllers/InstallController.php:202
* @route '/install/complete'
*/
completeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complete.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

complete.form = completeForm

const InstallController = { welcome, requirements, database, testDatabase, databaseStore, admin, adminStore, complete }

export default InstallController