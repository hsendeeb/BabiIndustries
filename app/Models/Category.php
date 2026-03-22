<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function setNameAttribute(?string $value): void
    {
        $value = is_string($value) ? trim($value) : $value;
        $this->attributes['name'] = filled($value) ? $value : null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $value = is_string($value) ? trim($value) : $value;
        $this->attributes['slug'] = filled($value) ? $value : null;
    }

    public function industries() {
        return $this->hasMany(Industry::class);
    }
}
