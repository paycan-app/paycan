import Orders from './Orders'
import Products from './Products'
import Subscriptions from './Subscriptions'
import Transactions from './Transactions'
import Users from './Users'

const Resources = {
    Orders: Object.assign(Orders, Orders),
    Products: Object.assign(Products, Products),
    Subscriptions: Object.assign(Subscriptions, Subscriptions),
    Transactions: Object.assign(Transactions, Transactions),
    Users: Object.assign(Users, Users),
}

export default Resources