import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
export const environment = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environment.url(options),
    method: 'get',
})

environment.definition = {
    methods: ["get","head"],
    url: '/install/environment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environment.url = (options?: RouteQueryOptions) => {
    return environment.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environment.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environment.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environment.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: environment.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
const environmentForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environment.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environment.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environment
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environment.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

environment.form = environmentForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
export const environmentWizard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environmentWizard.url(options),
    method: 'get',
})

environmentWizard.definition = {
    methods: ["get","head"],
    url: '/install/environment/wizard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
environmentWizard.url = (options?: RouteQueryOptions) => {
    return environmentWizard.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
environmentWizard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environmentWizard.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
environmentWizard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: environmentWizard.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
const environmentWizardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentWizard.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
environmentWizardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentWizard.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:44
* @route '/install/environment/wizard'
*/
environmentWizardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentWizard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

environmentWizard.form = environmentWizardForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
export const environmentSaveWizard = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: environmentSaveWizard.url(options),
    method: 'post',
})

environmentSaveWizard.definition = {
    methods: ["post"],
    url: '/install/environment/saveWizard',
} satisfies RouteDefinition<["post"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
environmentSaveWizard.url = (options?: RouteQueryOptions) => {
    return environmentSaveWizard.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
environmentSaveWizard.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: environmentSaveWizard.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
const environmentSaveWizardForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: environmentSaveWizard.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
environmentSaveWizardForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: environmentSaveWizard.url(options),
    method: 'post',
})

environmentSaveWizard.form = environmentSaveWizardForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
export const environmentClassic = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environmentClassic.url(options),
    method: 'get',
})

environmentClassic.definition = {
    methods: ["get","head"],
    url: '/install/environment/classic',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
environmentClassic.url = (options?: RouteQueryOptions) => {
    return environmentClassic.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
environmentClassic.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environmentClassic.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
environmentClassic.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: environmentClassic.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
const environmentClassicForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentClassic.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
environmentClassicForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentClassic.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:56
* @route '/install/environment/classic'
*/
environmentClassicForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentClassic.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

environmentClassic.form = environmentClassicForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
export const environmentSaveClassic = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: environmentSaveClassic.url(options),
    method: 'post',
})

environmentSaveClassic.definition = {
    methods: ["post"],
    url: '/install/environment/saveClassic',
} satisfies RouteDefinition<["post"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
environmentSaveClassic.url = (options?: RouteQueryOptions) => {
    return environmentSaveClassic.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
environmentSaveClassic.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: environmentSaveClassic.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
const environmentSaveClassicForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: environmentSaveClassic.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentSaveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
environmentSaveClassicForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: environmentSaveClassic.url(options),
    method: 'post',
})

environmentSaveClassic.form = environmentSaveClassicForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
export const permissions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: permissions.url(options),
    method: 'get',
})

permissions.definition = {
    methods: ["get","head"],
    url: '/install/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissions.url = (options?: RouteQueryOptions) => {
    return permissions.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: permissions.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: permissions.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
const permissionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: permissions.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: permissions.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\PermissionsController::permissions
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/PermissionsController.php:28
* @route '/install/permissions'
*/
permissionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: permissions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

permissions.form = permissionsForm

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
export const final = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: final.url(options),
    method: 'get',
})

final.definition = {
    methods: ["get","head"],
    url: '/install/final',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
final.url = (options?: RouteQueryOptions) => {
    return final.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
final.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: final.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
final.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: final.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
const finalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: final.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
*/
finalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: final.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\FinalController::final
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php:21
* @route '/install/final'
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

const LaravelInstaller = {
    environment: Object.assign(environment, environment),
    environmentWizard: Object.assign(environmentWizard, environmentWizard),
    environmentSaveWizard: Object.assign(environmentSaveWizard, environmentSaveWizard),
    environmentClassic: Object.assign(environmentClassic, environmentClassic),
    environmentSaveClassic: Object.assign(environmentSaveClassic, environmentSaveClassic),
    permissions: Object.assign(permissions, permissions),
    final: Object.assign(final, final),
}

export default LaravelInstaller