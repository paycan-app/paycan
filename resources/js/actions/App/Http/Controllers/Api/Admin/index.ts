import UserTokenController from './UserTokenController'
import UserController from './UserController'
import ProductController from './ProductController'
import OrderController from './OrderController'
import TransactionController from './TransactionController'
import WalletController from './WalletController'

const Admin = {
    UserTokenController: Object.assign(UserTokenController, UserTokenController),
    UserController: Object.assign(UserController, UserController),
    ProductController: Object.assign(ProductController, ProductController),
    OrderController: Object.assign(OrderController, OrderController),
    TransactionController: Object.assign(TransactionController, TransactionController),
    WalletController: Object.assign(WalletController, WalletController),
}

export default Admin