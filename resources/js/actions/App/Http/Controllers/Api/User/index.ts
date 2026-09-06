import AuthController from './AuthController'
import OrderController from './OrderController'
import SubscriptionController from './SubscriptionController'
import CheckoutController from './CheckoutController'
import TransactionController from './TransactionController'
import WalletController from './WalletController'
import ProductController from './ProductController'

const User = {
    AuthController: Object.assign(AuthController, AuthController),
    OrderController: Object.assign(OrderController, OrderController),
    SubscriptionController: Object.assign(SubscriptionController, SubscriptionController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
    TransactionController: Object.assign(TransactionController, TransactionController),
    WalletController: Object.assign(WalletController, WalletController),
    ProductController: Object.assign(ProductController, ProductController),
}

export default User