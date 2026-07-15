import ListUsers from './ListUsers'
import CreateUser from './CreateUser'
import ViewUser from './ViewUser'
import EditUser from './EditUser'

const Pages = {
    ListUsers: Object.assign(ListUsers, ListUsers),
    CreateUser: Object.assign(CreateUser, CreateUser),
    ViewUser: Object.assign(ViewUser, ViewUser),
    EditUser: Object.assign(EditUser, EditUser),
}

export default Pages