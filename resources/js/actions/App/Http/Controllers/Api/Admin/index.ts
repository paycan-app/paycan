import UserTokenController from './UserTokenController'
import UserController from './UserController'
import ProductController from './ProductController'
import OrderController from './OrderController'
import TransactionController from './TransactionController'

const Admin = {
    UserTokenController: Object.assign(UserTokenController, UserTokenController),
    UserController: Object.assign(UserController, UserController),
    ProductController: Object.assign(ProductController, ProductController),
    OrderController: Object.assign(OrderController, OrderController),
    TransactionController: Object.assign(TransactionController, TransactionController),
}

export default Admin