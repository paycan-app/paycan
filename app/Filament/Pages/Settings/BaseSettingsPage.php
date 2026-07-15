<?php

namespace App\Filament\Pages\Settings;

use App\Services\Settings\SettingsManager;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

abstract class BaseSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings.form';

    public ?array $data = [];

    /**
     * Get the settings provider instances for this page.
     * Each provider should implement the SettingProvider interface.
     *
     * @return array<\App\Contracts\SettingProvider>
     */
    abstract protected function getProviders(): array;

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function getSubNavigation(): array
    {
        return SettingsNavigation::getItems($this);
    }

    public function form(Schema $schema): Schema
    {
        $components = [];

        foreach ($this->getProviders() as $provider) {
            $components = array_merge($components, $provider->getSchema()->getComponents());
        }

        return $schema
            ->schema($components)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $manager = app(SettingsManager::class);

        foreach ($this->getProviders() as $provider) {
            $group = $provider->getGroup();
            $defaults = $provider->getDefaults();

            foreach ($defaults as $key => $default) {
                // Field name uses __ instead of . (e.g., app__name)
                $fieldName = str_replace('.', '__', "{$group}__{$key}");
                $value = data_get($data, $fieldName);

                $type = $this->inferType($key, $value);
                $isPublic = $this->isPublicField($key);

                // Save with dot notation (e.g., app.name)
                $manager->set("{$group}.{$key}", $value, $type, $isPublic);
            }
        }

        $manager->clearCache();

        // Clear locale session if app locale was changed
        foreach ($this->getProviders() as $provider) {
            if ($provider->getGroup() === 'app') {
                $defaults = $provider->getDefaults();
                if (isset($defaults['locale'])) {
                    session()->forget('locale');
                    break;
                }
            }
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function loadSettings(): void
    {
        $manager = app(SettingsManager::class);

        $this->data = [];

        foreach ($this->getProviders() as $provider) {
            $group = $provider->getGroup();
            $settings = $manager->getByGroup($group);
            $defaults = $provider->getDefaults();

            // Seed missing keys from defaults
            $seeded = false;
            foreach ($defaults as $key => $default) {
                if (! array_key_exists($key, $settings)) {
                    $type = $this->inferType($key, $default);
                    $isPublic = $this->isPublicField($key);
                    $manager->set("{$group}.{$key}", $default, $type, $isPublic);
                    $seeded = true;
                }
            }

            if ($seeded) {
                $manager->clearCache();
                $settings = $manager->getByGroup($group);
            }

            $settings = array_merge($defaults, $settings);

            foreach ($settings as $key => $value) {
                // Field name uses __ instead of . (e.g., app__name)
                $fieldName = str_replace('.', '__', "{$group}__{$key}");
                $this->data[$fieldName] = $value;
            }
        }
    }

    /**
     * Infer the type of a setting value.
     */
    protected function inferType(string $key, mixed $value): string
    {
        // Check common encrypted field names
        if (in_array($key, ['api_key', 'secret_key', 'webhook_secret', 'password', 'secret', 'token', 'client_secret'])) {
            return 'encrypted';
        }

        // Infer from value type
        return match (gettype($value)) {
            'boolean' => 'boolean',
            'integer' => 'integer',
            'array' => 'array',
            default => 'string',
        };
    }

    /**
     * Determine if a field should be publicly accessible.
     */
    protected function isPublicField(string $key): bool
    {
        // Sensitive fields are not public by default
        $sensitiveFields = ['api_key', 'secret', 'secret_key', 'password', 'webhook_secret', 'token', 'client_secret'];

        return ! in_array($key, $sensitiveFields);
    }
}
