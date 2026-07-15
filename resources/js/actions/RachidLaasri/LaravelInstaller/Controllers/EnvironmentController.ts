import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
export const environmentMenu = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environmentMenu.url(options),
    method: 'get',
})

environmentMenu.definition = {
    methods: ["get","head"],
    url: '/install/environment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentMenu.url = (options?: RouteQueryOptions) => {
    return environmentMenu.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentMenu.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: environmentMenu.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentMenu.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: environmentMenu.url(options),
    method: 'head',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
const environmentMenuForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentMenu.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentMenuForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentMenu.url(options),
    method: 'get',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::environmentMenu
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:34
* @route '/install/environment'
*/
environmentMenuForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: environmentMenu.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

environmentMenu.form = environmentMenuForm

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
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
export const saveWizard = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveWizard.url(options),
    method: 'post',
})

saveWizard.definition = {
    methods: ["post"],
    url: '/install/environment/saveWizard',
} satisfies RouteDefinition<["post"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
saveWizard.url = (options?: RouteQueryOptions) => {
    return saveWizard.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
saveWizard.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveWizard.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
const saveWizardForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveWizard.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveWizard
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:87
* @route '/install/environment/saveWizard'
*/
saveWizardForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveWizard.url(options),
    method: 'post',
})

saveWizard.form = saveWizardForm

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
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
export const saveClassic = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveClassic.url(options),
    method: 'post',
})

saveClassic.definition = {
    methods: ["post"],
    url: '/install/environment/saveClassic',
} satisfies RouteDefinition<["post"]>

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
saveClassic.url = (options?: RouteQueryOptions) => {
    return saveClassic.definition.url + queryParams(options)
}

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
saveClassic.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveClassic.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
const saveClassicForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveClassic.url(options),
    method: 'post',
})

/**
* @see \RachidLaasri\LaravelInstaller\Controllers\EnvironmentController::saveClassic
* @see vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php:70
* @route '/install/environment/saveClassic'
*/
saveClassicForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveClassic.url(options),
    method: 'post',
})

saveClassic.form = saveClassicForm

const EnvironmentController = { environmentMenu, environmentWizard, saveWizard, environmentClassic, saveClassic }

export default EnvironmentController