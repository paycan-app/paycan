<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Management')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Product Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Set $set, Get $get, string $operation) {
                                        // Only auto-generate slug if:
                                        // 1. Creating a new record OR
                                        // 2. Slug hasn't been manually edited
                                        $shouldAutoGenerate = $operation === 'create' || ! $get('slug_manually_edited');

                                        if ($state && $shouldAutoGenerate && ! $get('slug')) {
                                            $baseSlug = Str::slug(Str::ascii($state));
                                            $slug = $baseSlug;
                                            $counter = 1;
                                            
                                            // Ensure the slug is unique
                                            while (\App\Models\Product::where('slug', $slug)
                                                ->when(request()->route('record'), function ($query) {
                                                    $query->where('id', '!=', request()->route('record'));
                                                })
                                                ->exists()) {
                                                $slug = $baseSlug . '-' . $counter;
                                                $counter++;
                                            }
                                            
                                            $set('slug', $slug);
                                            // Make slug field visible so user can see the generated slug
                                            $set('slug_visible', true);
                                        }
                                    })
                                    ->hintActions([
                                        Action::make('viewId')
                                            ->label(fn (Get $get): string => 'ID: '.($get('id') ?: 'No ID'))
                                            ->color('gray')
                                            ->icon('heroicon-m-key')
                                            ->visible(fn (string $operation): bool => $operation === 'create')
                                            ->action(function (Set $set) {
                                                $set('id_visible', true);
                                            }),
                                        Action::make('viewSlug')
                                            ->label(fn (Get $get): string => $get('slug') ?: 'No slug')
                                            ->color('gray')
                                            ->icon('heroicon-m-pencil-square')
                                            ->action(function (Set $set) {
                                                $set('slug_visible', true);
                                                $set('slug_manually_edited', true);
                                            }),
                                    ])
                                    ->suffixAction(
                                        Action::make('showId')
                                            ->label(fn (Get $get): string => 'ID: '.($get('id') ?: 'N/A'))
                                            ->color('gray')
                                            ->disabled()
                                            ->visible(fn (string $operation): bool => $operation === 'edit')
                                    )
                                    ->placeholder('Enter product title'),
                                TextInput::make('id')
                                    ->label('Product ID')
                                    ->maxLength(50)
                                    ->placeholder('Optional: Leave empty to auto-generate')
                                    ->helperText('Custom ID (ULID will be generated if empty)')
                                    ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && (bool) $get('id_visible'))
                                    ->dehydrated(),
                                TextInput::make('id_visible')
                                    ->hidden()
                                    ->dehydrated(false)
                                    ->default(false),
                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->unique(
                                        table: 'products',
                                        column: 'slug',
                                        ignoreRecord: true,
                                        modifyRuleUsing: function ($rule, $component) {
                                            return $rule->withoutTrashed();
                                        }
                                    )
                                    ->maxLength(255)
                                    ->rules([
                                        'alpha_dash',
                                        function ($attribute, $value, $fail) {
                                            if (empty($value)) {
                                                return;
                                            }
                                            
                                            // Check for existing slug in database
                                            $exists = \App\Models\Product::where('slug', $value)
                                                ->when(request()->route('record'), function ($query) {
                                                    $query->where('id', '!=', request()->route('record'));
                                                })
                                                ->exists();
                                                
                                            if ($exists) {
                                                $fail('This URL slug is already taken. Please choose a different one.');
                                            }
                                        }
                                    ])
                                    ->helperText('Latin characters only. Must be unique across all products.')
                                    ->placeholder('product-url-slug')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                        if ($state) {
                                            // Clean the slug to ensure it follows proper format
                                            $cleanSlug = Str::slug($state);
                                            if ($cleanSlug !== $state) {
                                                $set('slug', $cleanSlug);
                                            }
                                        }
                                    }),
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
                                    ->searchable()
                                    ->helperText('Choose the type of product you are selling'),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->helperText('Whether this product is available for purchase')
                                    ->default(true),

                                Textarea::make('description')
                                    ->label('Product Description')
                                    ->rows(6)
                                    ->maxLength(65535)
                                    ->placeholder('Describe your product...')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                ViewField::make('pricing_widget')
                                    ->view('filament.products.pricing-2')
                                    ->viewData(fn (Get $get) => [
                                        'product' => $get('id')
                                            ? \App\Models\Product::query()->find($get('id'))
                                            : null,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Media & Files')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Product Image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                                    ->directory('products/images')
                                    ->visibility('public')
                                    ->helperText('Upload a high-quality product image'),

                                FileUpload::make('file')
                                    ->label('Digital File')
                                    ->directory('products/files')
                                    ->visibility('private')
                                    ->helperText('Upload digital content (for digital products only)')
                                    ->acceptedFileTypes(['application/pdf', 'application/zip', 'image/*', 'video/*'])
                                    ->visible(fn (Get $get): bool => $get('type') === 'digital'),
                            ]),
                        // Tab::make('Pricing')
                        //     ->icon('heroicon-o-currency-dollar')
                        //     ->schema([
                        //         Repeater::make('prices')
                        //             ->relationship()
                        //             ->schema([
                        //                 TextInput::make('title')
                        //                     ->label('Price Title')
                        //                     ->required()
                        //                     ->maxLength(255)
                        //                     ->live(onBlur: true)
                        //                     ->afterStateUpdated(function (?string $state, Set $set, Get $get, string $operation) {
                        //                         $shouldAutoGenerate = $operation === 'create' || ! $get('slug_manually_edited');
                        //                         if ($state && $shouldAutoGenerate && ! $get('slug')) {
                        //                             $slug = Str::slug(Str::ascii($state));
                        //                             $set('slug', $slug);
                        //                         }
                        //                     })
                        //                     ->hintActions([
                        //                         Action::make('viewSlug')
                        //                             ->label(fn (Get $get): string => $get('slug') ?: 'No slug')
                        //                             ->color('gray')
                        //                             ->icon('heroicon-m-pencil-square')
                        //                             ->action(function (Set $set) {
                        //                                 $set('slug_visible', true);
                        //                                 $set('slug_manually_edited', true);
                        //                             }),
                        //                     ])
                        //                     ->placeholder('e.g., Regular, Premium, Student')
                        //                     ->columnSpanFull(),
                        //                 TextInput::make('slug')
                        //                     ->label('Price Slug')
                        //                     ->maxLength(255)
                        //                     ->rules(['alpha_dash'])
                        //                     ->helperText('Latin characters only')
                        //                     ->placeholder('price-slug')
                        //                     ->visible(fn (Get $get): bool => (bool) $get('slug_visible'))
                        //                     ->columnSpanFull(),
                        //                 TextInput::make('slug_visible')
                        //                     ->hidden()
                        //                     ->dehydrated(false)
                        //                     ->default(false),
                        //                 TextInput::make('slug_manually_edited')
                        //                     ->hidden()
                        //                     ->dehydrated(false)
                        //                     ->default(false),
                        //                 TextInput::make('amount')
                        //                     ->label('Price')
                        //                     ->required()
                        //                     ->numeric()
                        //                     ->prefix('$')
                        //                     ->step(0.01)
                        //                     ->default(0),
                        //                 Select::make('currency')
                        //                     ->label('Currency')
                        //                     ->options([
                        //                         'USD' => 'USD ($)',
                        //                         'EUR' => 'EUR (€)',
                        //                         'GBP' => 'GBP (£)',
                        //                         'CAD' => 'CAD ($)',
                        //                     ])
                        //                     ->required()
                        //                     ->default('USD'),
                        //                 Select::make('billing_period')
                        //                     ->label('Billing Period')
                        //                     ->options([
                        //                         'once' => 'One-time',
                        //                         'daily' => 'Daily',
                        //                         'weekly' => 'Weekly',
                        //                         'monthly' => 'Monthly',
                        //                         'yearly' => 'Yearly',
                        //                     ])
                        //                     ->required()
                        //                     ->default('once'),
                        //                 TextInput::make('trial_days')
                        //                     ->label('Trial Days')
                        //                     ->numeric()
                        //                     ->default(0)
                        //                     ->minValue(0)
                        //                     ->helperText('Number of trial days (0 for no trial)'),
                        //                 Toggle::make('is_active')
                        //                     ->label('Active')
                        //                     ->default(true)
                        //                     ->helperText('Whether this price is available for purchase'),
                        //                 Textarea::make('description')
                        //                     ->label('Description')
                        //                     ->placeholder('Optional description for this pricing option')
                        //                     ->columnSpanFull()
                        //                     ->rows(3),
                        //             ])
                        //             ->columns(2)
                        //             ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Price')
                        //             ->collapsed()
                        //             ->cloneable()
                        //             ->collapsible()
                        //             ->defaultItems(0)
                        //             ->reorderable()
                        //             ->addActionLabel('Add Price Option')
                        //             ->deleteAction(
                        //                 fn ($action) => $action->requiresConfirmation()
                        //             )
                        //             ->columnSpanFull(),
                        //     ]),
                        
                        Tab::make('Metadata')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                KeyValue::make('meta')
                                    ->label('Product Metadata')
                                    ->keyLabel('Attribute Name')
                                    ->valueLabel('Attribute Value')
                                    ->reorderable()
                                    ->addActionLabel('Add Custom Field')
                                    ->helperText('Add custom product attributes and metadata')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
