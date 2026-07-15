import auth from './auth'
import paymentGateways from './payment-gateways'
import user from './user'
import integrate from './integrate'
import admin from './admin'
import webhooks from './webhooks'

const api = {
    auth: Object.assign(auth, auth),
    paymentGateways: Object.assign(paymentGateways, paymentGateways),
    user: Object.assign(user, user),
    integrate: Object.assign(integrate, integrate),
    admin: Object.assign(admin, admin),
    webhooks: Object.assign(webhooks, webhooks),
}

export default api