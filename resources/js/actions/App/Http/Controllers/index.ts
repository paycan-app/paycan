import InstallController from './InstallController'
import Api from './Api'
import PortalController from './PortalController'
import PortalDemoController from './PortalDemoController'
import CheckoutPageController from './CheckoutPageController'
import CheckoutPageDemoController from './CheckoutPageDemoController'
import AccountModalsDemoController from './AccountModalsDemoController'

const Controllers = {
    InstallController: Object.assign(InstallController, InstallController),
    Api: Object.assign(Api, Api),
    PortalController: Object.assign(PortalController, PortalController),
    PortalDemoController: Object.assign(PortalDemoController, PortalDemoController),
    CheckoutPageController: Object.assign(CheckoutPageController, CheckoutPageController),
    CheckoutPageDemoController: Object.assign(CheckoutPageDemoController, CheckoutPageDemoController),
    AccountModalsDemoController: Object.assign(AccountModalsDemoController, AccountModalsDemoController),
}

export default Controllers