<?php

namespace App\Rules;

use App\Services\SlugService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class UniqueSlugFromName implements ValidationRule
{
    public function __construct(
        private string $modelClass,
        private Model|int|string|null $ignore = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $slug = app(SlugService::class)->make($value);
        $query = $this->modelClass::query()->where('slug', $slug);
        $ignoreKey = $this->ignore instanceof Model ? $this->ignore->getKey() : $this->ignore;

        if ($ignoreKey !== null) {
            $query->whereKeyNot($ignoreKey);
        }

        if ($query->exists()) {
            $fail('The '.$attribute.' generates a slug that already exists.');
        }
    }
}
