<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = ['arquivo_id', 'corpo']; public function arquivo() { return $this->belongsTo(Arquivo::class); }
}
