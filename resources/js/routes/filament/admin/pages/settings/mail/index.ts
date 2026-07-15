import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
export const amazonSes = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: amazonSes.url(options),
    method: 'get',
})

amazonSes.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/amazon-ses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
amazonSes.url = (options?: RouteQueryOptions) => {
    return amazonSes.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
amazonSes.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: amazonSes.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
amazonSes.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: amazonSes.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
const amazonSesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: amazonSes.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
amazonSesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: amazonSes.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\AmazonSesSettings::__invoke
* @see app/Filament/Pages/Settings/AmazonSesSettings.php:7
* @route '/admin/settings/mail/amazon-ses'
*/
amazonSesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: amazonSes.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

amazonSes.form = amazonSesForm

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
export const mailgun = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mailgun.url(options),
    method: 'get',
})

mailgun.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/mailgun',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
mailgun.url = (options?: RouteQueryOptions) => {
    return mailgun.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
mailgun.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mailgun.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
mailgun.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mailgun.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
const mailgunForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mailgun.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
mailgunForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mailgun.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\MailgunSettings::__invoke
* @see app/Filament/Pages/Settings/MailgunSettings.php:7
* @route '/admin/settings/mail/mailgun'
*/
mailgunForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mailgun.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mailgun.form = mailgunForm

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
export const notifications = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notifications.url(options),
    method: 'get',
})

notifications.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
notifications.url = (options?: RouteQueryOptions) => {
    return notifications.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
notifications.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notifications.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
notifications.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notifications.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
const notificationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notifications.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
notificationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notifications.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\NotificationSettings::__invoke
* @see app/Filament/Pages/Settings/NotificationSettings.php:7
* @route '/admin/settings/mail/notifications'
*/
notificationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notifications.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

notifications.form = notificationsForm

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
export const postmark = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: postmark.url(options),
    method: 'get',
})

postmark.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/postmark',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
postmark.url = (options?: RouteQueryOptions) => {
    return postmark.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
postmark.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: postmark.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
postmark.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: postmark.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
const postmarkForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: postmark.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
postmarkForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: postmark.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\PostmarkSettings::__invoke
* @see app/Filament/Pages/Settings/PostmarkSettings.php:7
* @route '/admin/settings/mail/postmark'
*/
postmarkForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: postmark.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

postmark.form = postmarkForm

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
export const resend = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resend.url(options),
    method: 'get',
})

resend.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/resend',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
resend.url = (options?: RouteQueryOptions) => {
    return resend.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
resend.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resend.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
resend.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resend.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
const resendForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: resend.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
resendForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: resend.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\ResendSettings::__invoke
* @see app/Filament/Pages/Settings/ResendSettings.php:7
* @route '/admin/settings/mail/resend'
*/
resendForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: resend.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

resend.form = resendForm

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
export const sendmail = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sendmail.url(options),
    method: 'get',
})

sendmail.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/sendmail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
sendmail.url = (options?: RouteQueryOptions) => {
    return sendmail.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
sendmail.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sendmail.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
sendmail.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sendmail.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
const sendmailForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sendmail.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
sendmailForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sendmail.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SendmailSettings::__invoke
* @see app/Filament/Pages/Settings/SendmailSettings.php:7
* @route '/admin/settings/mail/sendmail'
*/
sendmailForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sendmail.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

sendmail.form = sendmailForm

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
export const smtp = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: smtp.url(options),
    method: 'get',
})

smtp.definition = {
    methods: ["get","head"],
    url: '/admin/settings/mail/smtp',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
smtp.url = (options?: RouteQueryOptions) => {
    return smtp.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
smtp.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: smtp.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
smtp.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: smtp.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
const smtpForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: smtp.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
smtpForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: smtp.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\SmtpSettings::__invoke
* @see app/Filament/Pages/Settings/SmtpSettings.php:7
* @route '/admin/settings/mail/smtp'
*/
smtpForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: smtp.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

smtp.form = smtpForm

const mail = {
    mailgun: Object.assign(mailgun, mailgun),
    notifications: Object.assign(notifications, notifications),
    postmark: Object.assign(postmark, postmark),
    resend: Object.assign(resend, resend),
    sendmail: Object.assign(sendmail, sendmail),
    smtp: Object.assign(smtp, smtp),
}

export default mail