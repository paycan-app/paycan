import ListTransactions from './ListTransactions'
import CreateTransaction from './CreateTransaction'
import ViewTransaction from './ViewTransaction'

const Pages = {
    ListTransactions: Object.assign(ListTransactions, ListTransactions),
    CreateTransaction: Object.assign(CreateTransaction, CreateTransaction),
    ViewTransaction: Object.assign(ViewTransaction, ViewTransaction),
}

export default Pages