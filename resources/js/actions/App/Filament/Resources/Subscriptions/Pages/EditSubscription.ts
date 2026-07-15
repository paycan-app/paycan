import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
const EditSubscription = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditSubscription.url(args, options),
    method: 'get',
})

EditSubscription.definition = {
    methods: ["get","head"],
    url: '/admin/subscriptions/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
EditSubscription.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditSubscription.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
EditSubscription.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditSubscription.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
EditSubscription.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditSubscription.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
const EditSubscriptionForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditSubscription.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
EditSubscriptionForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditSubscription.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Subscriptions\Pages\EditSubscription::__invoke
* @see app/Filament/Resources/Subscriptions/Pages/EditSubscription.php:7
* @route '/admin/subscriptions/{record}/edit'
*/
EditSubscriptionForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditSubscription.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EditSubscription.form = EditSubscriptionForm

export default EditSubscription