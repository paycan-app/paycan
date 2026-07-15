import EnvironmentController from './EnvironmentController'
import PermissionsController from './PermissionsController'
import FinalController from './FinalController'
import UpdateController from './UpdateController'

const Controllers = {
    EnvironmentController: Object.assign(EnvironmentController, EnvironmentController),
    PermissionsController: Object.assign(PermissionsController, PermissionsController),
    FinalController: Object.assign(FinalController, FinalController),
    UpdateController: Object.assign(UpdateController, UpdateController),
}

export default Controllers