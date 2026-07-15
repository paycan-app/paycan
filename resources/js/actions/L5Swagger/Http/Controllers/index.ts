import SwaggerController from './SwaggerController'
import SwaggerAssetController from './SwaggerAssetController'

const Controllers = {
    SwaggerController: Object.assign(SwaggerController, SwaggerController),
    SwaggerAssetController: Object.assign(SwaggerAssetController, SwaggerAssetController),
}

export default Controllers