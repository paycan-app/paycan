import AmazonSesSettings from './AmazonSesSettings'
import FacebookSettings from './FacebookSettings'
import FileDownloaderSettings from './FileDownloaderSettings'
import FulfillmentProvidersSettings from './FulfillmentProvidersSettings'
import GeneralSettings from './GeneralSettings'
import GitHubSettings from './GitHubSettings'
import GoogleSettings from './GoogleSettings'
import LicenseGeneratorSettings from './LicenseGeneratorSettings'
import MailSettings from './MailSettings'
import MailgunSettings from './MailgunSettings'
import NotificationSettings from './NotificationSettings'
import PayPalSettings from './PayPalSettings'
import PaymentProvidersSettings from './PaymentProvidersSettings'
import PostmarkSettings from './PostmarkSettings'
import ResendSettings from './ResendSettings'
import SendmailSettings from './SendmailSettings'
import SmtpSettings from './SmtpSettings'
import StripeSettings from './StripeSettings'

const Settings = {
    AmazonSesSettings: Object.assign(AmazonSesSettings, AmazonSesSettings),
    FacebookSettings: Object.assign(FacebookSettings, FacebookSettings),
    FileDownloaderSettings: Object.assign(FileDownloaderSettings, FileDownloaderSettings),
    FulfillmentProvidersSettings: Object.assign(FulfillmentProvidersSettings, FulfillmentProvidersSettings),
    GeneralSettings: Object.assign(GeneralSettings, GeneralSettings),
    GitHubSettings: Object.assign(GitHubSettings, GitHubSettings),
    GoogleSettings: Object.assign(GoogleSettings, GoogleSettings),
    LicenseGeneratorSettings: Object.assign(LicenseGeneratorSettings, LicenseGeneratorSettings),
    MailSettings: Object.assign(MailSettings, MailSettings),
    MailgunSettings: Object.assign(MailgunSettings, MailgunSettings),
    NotificationSettings: Object.assign(NotificationSettings, NotificationSettings),
    PayPalSettings: Object.assign(PayPalSettings, PayPalSettings),
    PaymentProvidersSettings: Object.assign(PaymentProvidersSettings, PaymentProvidersSettings),
    PostmarkSettings: Object.assign(PostmarkSettings, PostmarkSettings),
    ResendSettings: Object.assign(ResendSettings, ResendSettings),
    SendmailSettings: Object.assign(SendmailSettings, SendmailSettings),
    SmtpSettings: Object.assign(SmtpSettings, SmtpSettings),
    StripeSettings: Object.assign(StripeSettings, StripeSettings),
}

export default Settings