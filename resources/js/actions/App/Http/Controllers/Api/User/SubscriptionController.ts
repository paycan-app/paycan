import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/user/subscriptions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::index
* @see app/Http/Controllers/Api/User/SubscriptionController.php:129
* @route '/api/user/subscriptions'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::store
* @see app/Http/Controllers/Api/User/SubscriptionController.php:203
* @route '/api/user/subscriptions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/user/subscriptions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::store
* @see app/Http/Controllers/Api/User/SubscriptionController.php:203
* @route '/api/user/subscriptions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::store
* @see app/Http/Controllers/Api/User/SubscriptionController.php:203
* @route '/api/user/subscriptions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::store
* @see app/Http/Controllers/Api/User/SubscriptionController.php:203
* @route '/api/user/subscriptions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::store
* @see app/Http/Controllers/Api/User/SubscriptionController.php:203
* @route '/api/user/subscriptions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
export const show = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/user/subscriptions/{subscription}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
show.url = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subscription: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { subscription: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            subscription: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        subscription: typeof args.subscription === 'object'
        ? args.subscription.id
        : args.subscription,
    }

    return show.definition.url
            .replace('{subscription}', parsedArgs.subscription.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
show.get = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
show.head = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
const showForm = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
showForm.get = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::show
* @see app/Http/Controllers/Api/User/SubscriptionController.php:334
* @route '/api/user/subscriptions/{subscription}'
*/
showForm.head = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::cancel
* @see app/Http/Controllers/Api/User/SubscriptionController.php:413
* @route '/api/user/subscriptions/{subscription}/cancel'
*/
export const cancel = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/api/user/subscriptions/{subscription}/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::cancel
* @see app/Http/Controllers/Api/User/SubscriptionController.php:413
* @route '/api/user/subscriptions/{subscription}/cancel'
*/
cancel.url = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subscription: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { subscription: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            subscription: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        subscription: typeof args.subscription === 'object'
        ? args.subscription.id
        : args.subscription,
    }

    return cancel.definition.url
            .replace('{subscription}', parsedArgs.subscription.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::cancel
* @see app/Http/Controllers/Api/User/SubscriptionController.php:413
* @route '/api/user/subscriptions/{subscription}/cancel'
*/
cancel.post = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::cancel
* @see app/Http/Controllers/Api/User/SubscriptionController.php:413
* @route '/api/user/subscriptions/{subscription}/cancel'
*/
const cancelForm = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::cancel
* @see app/Http/Controllers/Api/User/SubscriptionController.php:413
* @route '/api/user/subscriptions/{subscription}/cancel'
*/
cancelForm.post = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

cancel.form = cancelForm

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::resume
* @see app/Http/Controllers/Api/User/SubscriptionController.php:525
* @route '/api/user/subscriptions/{subscription}/resume'
*/
export const resume = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resume.url(args, options),
    method: 'post',
})

resume.definition = {
    methods: ["post"],
    url: '/api/user/subscriptions/{subscription}/resume',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::resume
* @see app/Http/Controllers/Api/User/SubscriptionController.php:525
* @route '/api/user/subscriptions/{subscription}/resume'
*/
resume.url = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subscription: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { subscription: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            subscription: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        subscription: typeof args.subscription === 'object'
        ? args.subscription.id
        : args.subscription,
    }

    return resume.definition.url
            .replace('{subscription}', parsedArgs.subscription.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::resume
* @see app/Http/Controllers/Api/User/SubscriptionController.php:525
* @route '/api/user/subscriptions/{subscription}/resume'
*/
resume.post = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resume.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::resume
* @see app/Http/Controllers/Api/User/SubscriptionController.php:525
* @route '/api/user/subscriptions/{subscription}/resume'
*/
const resumeForm = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resume.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::resume
* @see app/Http/Controllers/Api/User/SubscriptionController.php:525
* @route '/api/user/subscriptions/{subscription}/resume'
*/
resumeForm.post = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resume.url(args, options),
    method: 'post',
})

resume.form = resumeForm

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::change
* @see app/Http/Controllers/Api/User/SubscriptionController.php:638
* @route '/api/user/subscriptions/{subscription}/change'
*/
export const change = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: change.url(args, options),
    method: 'post',
})

change.definition = {
    methods: ["post"],
    url: '/api/user/subscriptions/{subscription}/change',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::change
* @see app/Http/Controllers/Api/User/SubscriptionController.php:638
* @route '/api/user/subscriptions/{subscription}/change'
*/
change.url = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subscription: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { subscription: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            subscription: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        subscription: typeof args.subscription === 'object'
        ? args.subscription.id
        : args.subscription,
    }

    return change.definition.url
            .replace('{subscription}', parsedArgs.subscription.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::change
* @see app/Http/Controllers/Api/User/SubscriptionController.php:638
* @route '/api/user/subscriptions/{subscription}/change'
*/
change.post = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: change.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::change
* @see app/Http/Controllers/Api/User/SubscriptionController.php:638
* @route '/api/user/subscriptions/{subscription}/change'
*/
const changeForm = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: change.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\User\SubscriptionController::change
* @see app/Http/Controllers/Api/User/SubscriptionController.php:638
* @route '/api/user/subscriptions/{subscription}/change'
*/
changeForm.post = (args: { subscription: number | { id: number } } | [subscription: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: change.url(args, options),
    method: 'post',
})

change.form = changeForm

const SubscriptionController = { index, store, show, cancel, resume, change }

export default SubscriptionController