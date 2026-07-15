<?php

namespace App\Filament\Components;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

class JsonKeyValueViewer
{
    public static function make(string $field, ?string $label = null): RepeatableEntry
    {
        return RepeatableEntry::make($field)
            ->label($label ?? ucwords(str_replace('_', ' ', $field)))
            ->getStateUsing(function ($record) use ($field) {
                $state = $record->{$field} ?? [];

                if (is_string($state)) {
                    $decoded = json_decode($state, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $state = $decoded;
                    }
                }

                if (! is_array($state) || empty($state)) {
                    return [];
                }

                return collect($state)->map(function ($value, $key) {
                    $formatted = (is_scalar($value) || $value === null)
                        ? (string) ($value ?? '')
                        : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                    return [
                        'key' => (string) $key,
                        'value' => $formatted,
                    ];
                })->values()->all();
            })
            ->schema([
                TextEntry::make('key')->weight('medium')->columnSpan(3),
                TextEntry::make('value')->copyable()->columnSpan(9),
            ])
            ->columns(12)
            ->columnSpanFull()
            ->visible(function ($record) use ($field): bool {
                $state = $record->{$field} ?? [];
                if (is_string($state)) {
                    $decoded = json_decode($state, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $state = $decoded;
                    }
                }
                return is_array($state) && ! empty($state);
            });
    }
}