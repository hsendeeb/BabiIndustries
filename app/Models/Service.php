<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'industry_id'];

   

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
