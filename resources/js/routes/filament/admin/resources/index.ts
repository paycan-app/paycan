import orders from './orders'
import products from './products'
import subscriptions from './subscriptions'
import transactions from './transactions'
import users from './users'

const resources = {
    orders: Object.assign(orders, orders),
    products: Object.assign(products, products),
    subscriptions: Object.assign(subscriptions, subscriptions),
    transactions: Object.assign(transactions, transactions),
    users: Object.assign(users, users),
}

export default resources