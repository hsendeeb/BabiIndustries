<?php

namespace App\Services;

use Illuminate\Support\Str;

class SlugService
{
    public function make(string $value): string
    {
        return Str::slug(trim($value));
    }
}
