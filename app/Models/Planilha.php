<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planilha extends Model
{
    protected $fillable = ['arquivo_id', 'linhas', 'colunas']; 
    public function arquivo() { return $this->belongsTo(Arquivo::class); } 
    public function celulas() { return $this->hasMany(PlanilhaCelula::class); }
}
