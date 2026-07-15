import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/payment-gateways',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::index
* @see app/Http/Controllers/Api/PaymentGatewayController.php:20
* @route '/api/payment-gateways'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
export const forProduct = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forProduct.url(args, options),
    method: 'get',
})

forProduct.definition = {
    methods: ["get","head"],
    url: '/api/payment-gateways/products/{product}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
forProduct.url = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { product: args }
    }

    if (Array.isArray(args)) {
        args = {
            product: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product: args.product,
    }

    return forProduct.definition.url
            .replace('{product}', parsedArgs.product.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
forProduct.get = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forProduct.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
forProduct.head = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: forProduct.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
const forProductForm = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forProduct.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
forProductForm.get = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forProduct.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProduct
* @see app/Http/Controllers/Api/PaymentGatewayController.php:43
* @route '/api/payment-gateways/products/{product}'
*/
forProductForm.head = (args: { product: string | number } | [product: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forProduct.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

forProduct.form = forProductForm

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
export const forProductPrice = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forProductPrice.url(args, options),
    method: 'get',
})

forProductPrice.definition = {
    methods: ["get","head"],
    url: '/api/payment-gateways/product-prices/{productPrice}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
forProductPrice.url = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { productPrice: args }
    }

    if (Array.isArray(args)) {
        args = {
            productPrice: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        productPrice: args.productPrice,
    }

    return forProductPrice.definition.url
            .replace('{productPrice}', parsedArgs.productPrice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
forProductPrice.get = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forProductPrice.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
forProductPrice.head = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: forProductPrice.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
const forProductPriceForm = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forProductPrice.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
forProductPriceForm.get = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forProductPrice.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::forProductPrice
* @see app/Http/Controllers/Api/PaymentGatewayController.php:65
* @route '/api/payment-gateways/product-prices/{productPrice}'
*/
forProductPriceForm.head = (args: { productPrice: string | number } | [productPrice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forProductPrice.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

forProductPrice.form = forProductPriceForm

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::validate
* @see app/Http/Controllers/Api/PaymentGatewayController.php:87
* @route '/api/payment-gateways/validate'
*/
export const validate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: validate.url(options),
    method: 'post',
})

validate.definition = {
    methods: ["post"],
    url: '/api/payment-gateways/validate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::validate
* @see app/Http/Controllers/Api/PaymentGatewayController.php:87
* @route '/api/payment-gateways/validate'
*/
validate.url = (options?: RouteQueryOptions) => {
    return validate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::validate
* @see app/Http/Controllers/Api/PaymentGatewayController.php:87
* @route '/api/payment-gateways/validate'
*/
validate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: validate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::validate
* @see app/Http/Controllers/Api/PaymentGatewayController.php:87
* @route '/api/payment-gateways/validate'
*/
const validateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: validate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PaymentGatewayController::validate
* @see app/Http/Controllers/Api/PaymentGatewayController.php:87
* @route '/api/payment-gateways/validate'
*/
validateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: validate.url(options),
    method: 'post',
})

validate.form = validateForm

const PaymentGatewayController = { index, forProduct, forProductPrice, validate }

export default PaymentGatewayController