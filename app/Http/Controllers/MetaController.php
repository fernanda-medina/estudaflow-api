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

    public function index(Request $request)
{
    return response()->json(
        $request->user()->metas()->orderBy('prazo')->get()
    );
}

public function show(Request $request, $id)
{
    $meta = $request->user()->metas()->findOrFail($id);

    return response()->json($meta);
}

public function update(Request $request, $id)
{
    $meta = $request->user()->metas()->findOrFail($id);

    $dados = $request->validate([
        'nome'       => 'sometimes|required|string|max:255',
        'disciplina' => 'sometimes|nullable|string|max:255',
        'prazo'      => 'sometimes|nullable|date',
        'prioridade' => 'sometimes|in:normal,urgente,baixa',
        'done'       => 'sometimes|boolean',
    ]);

    $meta->update($dados);

    return response()->json($meta);
}

public function destroy(Request $request, $id)
{
    $meta = $request->user()->metas()->findOrFail($id);
    $meta->delete();

    return response()->json(['message' => 'Meta apagada.']);
}

}