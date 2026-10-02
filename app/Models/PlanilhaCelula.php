<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanilhaCelula extends Model
{
    protected $fillable = ['planilha_id', 'linha', 'coluna', 'valor'];
    public function planilha() { return $this->belongsTo(Planilha::class); }
}
