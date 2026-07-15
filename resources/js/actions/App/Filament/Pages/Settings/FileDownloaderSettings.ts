import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
const FileDownloaderSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: FileDownloaderSettings.url(options),
    method: 'get',
})

FileDownloaderSettings.definition = {
    methods: ["get","head"],
    url: '/admin/settings/fulfillment/file-downloader',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
FileDownloaderSettings.url = (options?: RouteQueryOptions) => {
    return FileDownloaderSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
FileDownloaderSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: FileDownloaderSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
FileDownloaderSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: FileDownloaderSettings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
const FileDownloaderSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FileDownloaderSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
FileDownloaderSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FileDownloaderSettings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\Settings\FileDownloaderSettings::__invoke
* @see app/Filament/Pages/Settings/FileDownloaderSettings.php:7
* @route '/admin/settings/fulfillment/file-downloader'
*/
FileDownloaderSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: FileDownloaderSettings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

FileDownloaderSettings.form = FileDownloaderSettingsForm

export default FileDownloaderSettings