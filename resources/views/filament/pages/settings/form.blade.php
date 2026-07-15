<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-5 flex items-center justify-end gap-x-3" style="margin-top: 1.5rem;">
            <x-filament::button type="submit" size="lg">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
