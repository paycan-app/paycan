import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
export const css = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: css.url(options),
    method: 'get',
})

css.definition = {
    methods: ["get","head"],
    url: '/_debugbar/assets/stylesheets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
css.url = (options?: RouteQueryOptions) => {
    return css.definition.url + queryParams(options)
}

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
css.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: css.url(options),
    method: 'get',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
css.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: css.url(options),
    method: 'head',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
const cssForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: css.url(options),
    method: 'get',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
cssForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: css.url(options),
    method: 'get',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::css
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:36
* @route '/_debugbar/assets/stylesheets'
*/
cssForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: css.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

css.form = cssForm

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
export const js = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: js.url(options),
    method: 'get',
})

js.definition = {
    methods: ["get","head"],
    url: '/_debugbar/assets/javascript',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
js.url = (options?: RouteQueryOptions) => {
    return js.definition.url + queryParams(options)
}

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
js.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: js.url(options),
    method: 'get',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
js.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: js.url(options),
    method: 'head',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
const jsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: js.url(options),
    method: 'get',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
jsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: js.url(options),
    method: 'get',
})

/**
* @see \Barryvdh\Debugbar\Controllers\AssetController::js
* @see vendor/barryvdh/laravel-debugbar/src/Controllers/AssetController.php:14
* @route '/_debugbar/assets/javascript'
*/
jsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: js.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

js.form = jsForm

const assets = {
    css: Object.assign(css, css),
    js: Object.assign(js, js),
}

export default assets