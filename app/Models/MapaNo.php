<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapaNo extends Model
{
    protected $fillable = ['mapa_mental_id', 'parent_id', 'label', 'x', 'y', 'cor'];

    public function mapaMental() { return $this->belongsTo(MapaMental::class); }
    public function parent() { return $this->belongsTo(MapaNo::class, 'parent_id'); }
    public function filhos() { return $this->hasMany(MapaNo::class, 'parent_id'); }
}