import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
export const download = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/filament/imports/{import}/failed-rows/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
download.url = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { import: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { import: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            import: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        import: typeof args.import === 'object'
        ? args.import.id
        : args.import,
    }

    return download.definition.url
            .replace('{import}', parsedArgs.import.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
download.get = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
download.head = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
const downloadForm = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
downloadForm.get = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv::__invoke
* @see vendor/filament/actions/src/Imports/Http/Controllers/DownloadImportFailureCsv.php:17
* @route '/filament/imports/{import}/failed-rows/download'
*/
downloadForm.head = (args: { import: string | number | { id: string | number } } | [importParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

const failedRows = {
    download: Object.assign(download, download),
}

export default failedRows