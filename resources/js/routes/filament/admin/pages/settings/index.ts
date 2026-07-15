import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
import mail14724b from './mail'
import socialLogin from './social-login'
import fulfillment from './fulfillment'
import paymentProviders32982e from './payment-providers'
/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
export const fulfillmentProviders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fulfillmentProviders.url(options),
    method: 'get',
})

fulfillmentProviders.definition = {
    methods: ["get","head"],
    url: '/admin/settings/fulfillment-providers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
fulfillmentProviders.url = (options?: RouteQueryOptions) => {
    return fulfillmentProviders.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
fulfillmentProviders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fulfillmentProviders.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
fulfillmentProviders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fulfillmentProviders.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
const fulfillmentProvidersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fulfillmentProviders.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
fulfillmentProvidersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fulfillmentProviders.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FulfillmentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/FulfillmentProvidersSettings.php:7
* @route '/admin/settings/fulfillment-providers'
*/
fulfillmentProvidersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fulfillmentProviders.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

fulfillmentProviders.form = fulfillmentProvidersForm

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
export const general = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: general.url(options),
    method: 'get',
})

general.definition = {
    methods: ["get","head"],
    url: '/admin/settings/general',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
general.url = (options?: RouteQueryOptions) => {
    return general.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
general.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: general.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
general.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: general.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
const generalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: general.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
generalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: general.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\GeneralSettings::__invoke
* @see app/Filament/Pages/Settings/GeneralSettings.php:7
* @route '/admin/settings/general'
*/
generalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: general.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

general.form = generalForm

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
export const mail = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mail.url(options),
    method: 'get',
})

mail.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
mail.url = (options?: RouteQueryOptions) => {
    return mail.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
mail.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mail.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
mail.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mail.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
const mailForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mail.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
mailForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mail.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailSettings::__invoke
* @see app/Filament/Pages/Settings/MailSettings.php:7
* @route '/admin/settings/mail'
*/
mailForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mail.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mail.form = mailForm

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
export const paymentProviders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentProviders.url(options),
    method: 'get',
})

paymentProviders.definition = {
    methods: ["get","head"],
    url: '/admin/settings/payment-providers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
paymentProviders.url = (options?: RouteQueryOptions) => {
    return paymentProviders.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
paymentProviders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentProviders.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
paymentProviders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: paymentProviders.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
const paymentProvidersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentProviders.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
paymentProvidersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentProviders.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PaymentProvidersSettings::__invoke
* @see app/Filament/Pages/Settings/PaymentProvidersSettings.php:7
* @route '/admin/settings/payment-providers'
*/
paymentProvidersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: paymentProviders.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

paymentProviders.form = paymentProvidersForm

const settings = {
    mail: Object.assign(mail, mail14724b),
    socialLogin: Object.assign(socialLogin, socialLogin),
    fulfillment: Object.assign(fulfillment, fulfillment),
    fulfillmentProviders: Object.assign(fulfillmentProviders, fulfillmentProviders),
    general: Object.assign(general, general),
    paymentProviders: Object.assign(paymentProviders, paymentProviders32982e),
}

export default settings