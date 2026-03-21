<?php

namespace App\Filament\Resources\Industries\Pages;

use App\Filament\Resources\Industries\IndustryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateIndustry extends CreateRecord
{
    protected static string $resource = IndustryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        return $data;
    }
}
