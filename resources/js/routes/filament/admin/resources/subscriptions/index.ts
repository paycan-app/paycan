import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/subscriptions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ListSubscriptions::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ListSubscriptions.php:7
* @route '/admin/subscriptions'
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
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
export const edit = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/subscriptions/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
edit.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return edit.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
edit.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
edit.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
const editForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
editForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
editForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
export const view = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/admin/subscriptions/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
view.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return view.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
view.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
view.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
const viewForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
viewForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\ViewSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/ViewSubscription.php:7
* @route '/admin/subscriptions/{record}'
*/
viewForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

view.form = viewForm

const subscriptions = {
    index: Object.assign(index, index),
    edit: Object.assign(edit, edit),
    view: Object.assign(view, view),
}

export default subscriptions