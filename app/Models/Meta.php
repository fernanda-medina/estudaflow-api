<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    //
 protected $fillable = ['user_id', 'nome', 'disciplina', 'prazo', 'prioridade', 'done']; public function user() { return $this->belongsTo(User::class); }}
