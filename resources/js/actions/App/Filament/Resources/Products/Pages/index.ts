import ListProducts from './ListProducts'
import CreateProduct from './CreateProduct'
import EditProduct from './EditProduct'

const Pages = {
    ListProducts: Object.assign(ListProducts, ListProducts),
    CreateProduct: Object.assign(CreateProduct, CreateProduct),
    EditProduct: Object.assign(EditProduct, EditProduct),
}

export default Pages