<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetaController extends Controller
{
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'       => 'required|string|max:255',
            'disciplina' => 'required|string|max:255',
            'prazo'      => 'required|date|after_or_equal:today',
            'prioridade' => 'nullable|in:normal,urgente,baixa',
        ]);

        $meta = $request->user()->metas()->create($dados);

        return response()->json($meta->fresh(), 201);
    }
}