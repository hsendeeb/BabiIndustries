<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = 'industries';
    protected $fillable = ['name', 'slug', 'description','category_id'];
 
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
   
}
