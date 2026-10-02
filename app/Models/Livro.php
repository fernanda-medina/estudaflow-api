<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    protected $fillable = ['titulo', 'autor', 'area', 'icon']; public function tags() { return $this->belongsToMany(Tag::class); } 
    public function leitores() { return $this->belongsToMany(User::class, 'lista_leitura'); }
}
