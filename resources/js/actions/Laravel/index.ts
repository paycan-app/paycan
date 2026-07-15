import Cashier from './Cashier'
import Sanctum from './Sanctum'
import Telescope from './Telescope'

const Laravel = {
    Cashier: Object.assign(Cashier, Cashier),
    Sanctum: Object.assign(Sanctum, Sanctum),
    Telescope: Object.assign(Telescope, Telescope),
}

export default Laravel