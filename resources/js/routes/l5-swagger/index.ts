import admin from './admin'
import user from './user'

const l5Swagger = {
    admin: Object.assign(admin, admin),
    user: Object.assign(user, user),
}

export default l5Swagger