<?php

namespace App\Filament\Pages\Settings;

class FileDownloaderSettings extends BaseSettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'File Downloader';

    protected static ?string $title = 'File Downloader Settings';

    protected static ?int $navigationSort = 41;

    protected static ?string $slug = 'settings/fulfillment/file-downloader';

    protected static bool $shouldRegisterNavigation = false;

    protected function getProviders(): array
    {
        return [
            app(\App\Services\Settings\Providers\FileDownloaderSettingsProvider::class),
        ];
    }
}
