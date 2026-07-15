import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
export const fileDownloader = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fileDownloader.url(options),
    method: 'get',
})

fileDownloader.definition = {
    methods: ["get","head"],
    url: '/admin/settings/fulfillment/file-downloader',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
fileDownloader.url = (options?: RouteQueryOptions) => {
    return fileDownloader.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
fileDownloader.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fileDownloader.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
fileDownloader.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fileDownloader.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
const fileDownloaderForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fileDownloader.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
fileDownloaderForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fileDownloader.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
fileDownloaderForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fileDownloader.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

fileDownloader.form = fileDownloaderForm

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
export const licenseGenerator = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: licenseGenerator.url(options),
    method: 'get',
})

licenseGenerator.definition = {
    methods: ["get","head"],
    url: '/admin/settings/fulfillment/license-generator',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
licenseGenerator.url = (options?: RouteQueryOptions) => {
    return licenseGenerator.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
licenseGenerator.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: licenseGenerator.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
licenseGenerator.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: licenseGenerator.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
const licenseGeneratorForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: licenseGenerator.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
licenseGeneratorForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: licenseGenerator.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\LicenseGeneratorSettings::__invoke
* @see app/Filament/Pages/Settings/LicenseGeneratorSettings.php:7
* @route '/admin/settings/fulfillment/license-generator'
*/
licenseGeneratorForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: licenseGenerator.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

licenseGenerator.form = licenseGeneratorForm

const fulfillment = {
    fileDownloader: Object.assign(fileDownloader, fileDownloader),
    licenseGenerator: Object.assign(licenseGenerator, licenseGenerator),
}

export default fulfillment