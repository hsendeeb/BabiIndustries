<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Industry extends Model
{
    protected $table = 'industries';
    protected $fillable = ['name', 'slug', 'description', 'icon', 'category_id'];

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

    public function setDescriptionAttribute(?string $value): void
    {
        $value = is_string($value) ? trim($value) : $value;
        $this->attributes['description'] = filled($value) ? $value : null;
    }

    public function setIconAttribute(?string $value): void
    {
        $value = is_string($value) ? trim($value) : $value;
        $this->attributes['icon'] = filled($value) ? $value : null;
    }
 
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function services() {
        return $this->hasMany(Service::class);
    }
    public function category() {
        return $this->belongsTo(Category::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Industry $industry): void {
            if (empty($industry->created_by) && Auth::check()) {
                $industry->created_by = Auth::id();
            }
        });
    }
   
}
