<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
   protected $fillable = ['user_id', 'nome', 'tipo', 'tamanho_kb']; public function user() { return $this->belongsTo(User::class); } public function documento() { return $this->hasOne(Documento::class); } public function mapaMental() { return $this->hasOne(MapaMental::class); } 
   public function planilha() { return $this->hasOne(Planilha::class); } 
   public function apresentacao() { return $this->hasOne(Apresentacao::class); }
}
