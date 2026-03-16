<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = 'industries';
    protected $fillable = ['name', 'slug', 'description'];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   
}
