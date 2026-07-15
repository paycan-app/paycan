import LaravelInstaller from './LaravelInstaller'
import LaravelUpdater from './LaravelUpdater'

const namespaced = {
    LaravelInstaller: Object.assign(LaravelInstaller, LaravelInstaller),
    LaravelUpdater: Object.assign(LaravelUpdater, LaravelUpdater),
}

export default namespaced