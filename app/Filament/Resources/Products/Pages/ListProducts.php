<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Models\Product;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Actions\Action as BaseAction;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addProduct')
                ->label('Add Product')
                ->modalHeading('Create Product')
                ->icon('heroicon-o-plus')
                ->modalSubmitActionLabel('Next')
                ->form([
                    TextInput::make('title')
                        ->label('Product Title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                            $shouldAutoGenerate = ! $get('slug_manually_edited');

                            if ($state && $shouldAutoGenerate && ! $get('slug')) {
                                $slug = Str::slug(Str::ascii($state));
                                $set('slug', $slug);
                            }
                        })
                        ->hintActions([
                            BaseAction::make('viewSlug')
                                ->label(fn (Get $get): string => $get('slug') ?: 'No slug')
                                ->color('gray')
                                ->icon('heroicon-m-pencil-square')
                                ->action(function (Set $set) {
                                    $set('slug_visible', true);
                                    $set('slug_manually_edited', true);
                                }),
                        ]),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->maxLength(255)
                        ->unique(
                            table: 'products',
                            column: 'slug',
                            modifyRuleUsing: function ($rule) {
                                return $rule->withoutTrashed();
                            }
                        )
                        ->rules([
                            'alpha_dash',
                            function ($attribute, $value, $fail) {
                                if (empty($value)) {
                                    return;
                                }
                                
                                // Check for existing slug in database
                                $exists = Product::where('slug', $value)->exists();
                                    
                                if ($exists) {
                                    $fail('This URL slug is already taken. Please choose a different one.');
                                }
                            }
                        ])
                        ->placeholder('product-slug')
                        ->helperText('Must be unique across all products')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            if ($state) {
                                // Clean the slug to ensure it follows proper format
                                $cleanSlug = Str::slug($state);
                                if ($cleanSlug !== $state) {
                                    $set('slug', $cleanSlug);
                                }
                            }
                        })
                        ->visible(fn (Get $get): bool => (bool) $get('slug_visible')),
                    TextInput::make('slug_visible')
                        ->hidden()
                        ->dehydrated(false)
                        ->default(false),
                    TextInput::make('slug_manually_edited')
                        ->hidden()
                        ->dehydrated(false)
                        ->default(false),
                    Select::make('type')
                        ->label('Product Type')
                        ->options(fn () => array_combine(Product::getTypes(), array_map('ucfirst', Product::getTypes())))
                        ->required()
                        ->searchable(),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Textarea::make('description')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    try {
                        $product = Product::create([
                            'title' => $data['title'],
                            'slug' => $data['slug'] ?? null,
                            'type' => $data['type'],
                            'is_active' => $data['is_active'] ?? true,
                            'description' => $data['description'] ?? null,
                        ]);

                        $this->redirect(ProductResource::getUrl('edit', ['record' => $product]) . '?tab=pricing');
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Handle unique constraint violations
                        if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                            if (str_contains($e->getMessage(), 'products.slug')) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Duplicate URL Slug')
                                    ->body('A product with this URL slug already exists. Please choose a different slug.')
                                    ->danger()
                                    ->send();
                                return;
                            } elseif (str_contains($e->getMessage(), 'products.id')) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Duplicate Product ID')
                                    ->body('A product with this ID already exists. Please choose a different ID or leave it empty to auto-generate.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }
                        
                        // For other database errors, show a generic error message
                        \Filament\Notifications\Notification::make()
                            ->title('Error Creating Product')
                            ->body('An error occurred while creating the product. Please try again.')
                            ->danger()
                            ->send();
                    }
                })
                ->modalWidth('xl'),
        ];
    }
}
