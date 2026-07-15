import users from './users'
import products from './products'
import orders from './orders'
import settings from './settings'
import transactions from './transactions'

const admin = {
    users: Object.assign(users, users),
    products: Object.assign(products, products),
    orders: Object.assign(orders, orders),
    settings: Object.assign(settings, settings),
    transactions: Object.assign(transactions, transactions),
}

export default admin