<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapaMental extends Model
{
    protected $fillable = ['arquivo_id'];

    public function arquivo() { return $this->belongsTo(Arquivo::class); }
    public function nos() { return $this->hasMany(MapaNo::class); }
}