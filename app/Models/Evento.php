<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    //
protected $fillable = ['user_id', 'titulo', 'data', 'hora', 'tipo']; public function user() { return $this->belongsTo(User::class); }}
