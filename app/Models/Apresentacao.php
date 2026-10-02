<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apresentacao extends Model
{
    protected $fillable = ['arquivo_id']; public function arquivo() { return $this->belongsTo(Arquivo::class); } 
    public function slides() { return $this->hasMany(Slide::class); }
}
