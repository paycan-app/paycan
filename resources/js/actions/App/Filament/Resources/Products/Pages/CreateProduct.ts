import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
const CreateProduct = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateProduct.url(options),
    method: 'get',
})

CreateProduct.definition = {
    methods: ["get","head"],
    url: '/admin/products/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
CreateProduct.url = (options?: RouteQueryOptions) => {
    return CreateProduct.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
CreateProduct.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateProduct.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
CreateProduct.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateProduct.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
const CreateProductForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateProduct.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
CreateProductForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateProduct.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Products\Pages\CreateProduct::__invoke
* @see app/Filament/Resources/Products/Pages/CreateProduct.php:7
* @route '/admin/products/create'
*/
CreateProductForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateProduct.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CreateProduct.form = CreateProductForm

export default CreateProduct