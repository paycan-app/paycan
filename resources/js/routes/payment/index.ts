import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:52
* @route '/payment/success'
*/
export const success = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(options),
    method: 'get',
})

success.definition = {
    methods: ["get","head"],
    url: '/payment/success',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:52
* @route '/payment/success'
*/
success.url = (options?: RouteQueryOptions) => {
    return success.definition.url + queryParams(options)
}

/**
* @see routes/web.php:52
* @route '/payment/success'
*/
success.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(options),
    method: 'get',
})

/**
* @see routes/web.php:52
* @route '/payment/success'
*/
success.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: success.url(options),
    method: 'head',
})

/**
* @see routes/web.php:52
* @route '/payment/success'
*/
const successForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: success.url(options),
    method: 'get',
})

/**
* @see routes/web.php:52
* @route '/payment/success'
*/
successForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: success.url(options),
    method: 'get',
})

/**
* @see routes/web.php:52
* @route '/payment/success'
*/
successForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: success.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

success.form = successForm

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
export const cancel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cancel.url(options),
    method: 'get',
})

cancel.definition = {
    methods: ["get","head"],
    url: '/payment/cancel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
cancel.url = (options?: RouteQueryOptions) => {
    return cancel.definition.url + queryParams(options)
}

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
cancel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cancel.url(options),
    method: 'get',
})

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
cancel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cancel.url(options),
    method: 'head',
})

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
const cancelForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: cancel.url(options),
    method: 'get',
})

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
cancelForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: cancel.url(options),
    method: 'get',
})

/**
* @see routes/web.php:89
* @route '/payment/cancel'
*/
cancelForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: cancel.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

cancel.form = cancelForm

const payment = {
    success: Object.assign(success, success),
    cancel: Object.assign(cancel, cancel),
}

export default payment