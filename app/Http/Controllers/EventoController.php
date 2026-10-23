<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->eventos()->orderBy('data')->orderBy('hora')->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'data'   => 'required|date',
            'hora'   => 'required|date_format:H:i',
            'tipo'   => 'required|in:aula,prova,entrega,estudo',
        ]);

        $evento = $request->user()->eventos()->create($dados);

        return response()->json($evento, 201);
    }

    public function show(Request $request, $id)
    {
        $evento = $request->user()->eventos()->findOrFail($id);

        return response()->json($evento);
    }

    public function update(Request $request, $id)
    {
        $evento = $request->user()->eventos()->findOrFail($id);

        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'data'   => 'sometimes|required|date',
            'hora'   => 'sometimes|required|date_format:H:i',
            'tipo'   => 'sometimes|required|in:aula,prova,entrega,estudo',
        ]);

        $evento->update($dados);

        return response()->json($evento);
    }

    public function destroy(Request $request, $id)
    {
        $evento = $request->user()->eventos()->findOrFail($id);
        $evento->delete();

        return response()->json(['message' => 'Evento apagado.']);
    }
}