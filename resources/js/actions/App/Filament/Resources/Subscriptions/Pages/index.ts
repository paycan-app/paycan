import ListSubscriptions from './ListSubscriptions'
import EditSubscription from './EditSubscription'
import ViewSubscription from './ViewSubscription'

const Pages = {
    ListSubscriptions: Object.assign(ListSubscriptions, ListSubscriptions),
    EditSubscription: Object.assign(EditSubscription, EditSubscription),
    ViewSubscription: Object.assign(ViewSubscription, ViewSubscription),
}

export default Pages