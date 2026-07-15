import User from './User'
import PaymentGatewayController from './PaymentGatewayController'
import Admin from './Admin'
import SettingsController from './SettingsController'
import WebhookController from './WebhookController'

const Api = {
    User: Object.assign(User, User),
    PaymentGatewayController: Object.assign(PaymentGatewayController, PaymentGatewayController),
    Admin: Object.assign(Admin, Admin),
    SettingsController: Object.assign(SettingsController, SettingsController),
    WebhookController: Object.assign(WebhookController, WebhookController),
}

export default Api