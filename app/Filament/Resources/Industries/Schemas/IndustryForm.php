<?php

namespace App\Filament\Resources\Industries\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class IndustryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled(trim((string) $state)) ? trim((string) $state) : null)
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('slug', Str::slug($state ?? ''));
                    })
                    ->unique(ignoreRecord: true),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled(trim((string) $state)) ? trim((string) $state) : null)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(4),
                TextInput::make('icon')
                    ->label('Icon')
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled(trim((string) $state)) ? trim((string) $state) : null)
                    ->helperText('Store an icon value, for example: heroicon-o-building-office-2'),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Repeater::make('services')
                    ->relationship('services')
                    ->label('Services')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled(trim((string) $state)) ? trim((string) $state) : null)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('slug', Str::slug($state ?? ''));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled(trim((string) $state)) ? trim((string) $state) : null),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel('Add service')
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
