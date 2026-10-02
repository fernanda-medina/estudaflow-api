<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
   protected $fillable = ['apresentacao_id', 'ordem', 'titulo', 'corpo']; 
   public function apresentacao() { return $this->belongsTo(Apresentacao::class); }
}
