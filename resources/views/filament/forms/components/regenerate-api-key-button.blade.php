<div class="mt-1" style="width: 100%;">
    <div class="flex items-center justify-end" style="width: 100%;">
        <x-filament::button
            color="gray"
            size="sm"
            icon="heroicon-o-arrow-path"
            wire:click="regenerateApiKey"
            wire:confirm="Are you sure? This will invalidate the current key and all API requests using it will fail."
        >
            Regenerate API Key
        </x-filament::button>
    </div>

    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-left">
        Click to generate a new API key. This will invalidate the current key.
    </p>
</div>
