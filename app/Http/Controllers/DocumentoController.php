<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        return Documento::with('arquivo')
            ->whereHas('arquivo', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->get()
            ->map(fn (Documento $d) => $this->formatar($d));
    }

    public function show(Request $request, Documento $documento)
    {
        return $this->formatar($this->doDono($request, $documento));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'  => 'required|string|max:255',
            'corpo' => 'nullable|string',
        ]);

        $documento = DB::transaction(function () use ($request, $dados) {
            $arquivo = Arquivo::create([
                'user_id'    => $request->user()->id,
                'nome'       => $dados['nome'],
                'tipo'       => 'documento',
                'tamanho_kb' => $this->tamanhoKb($dados['corpo'] ?? ''),
            ]);

            return $arquivo->documento()->create(['corpo' => $dados['corpo'] ?? null]);
        });

        return response()->json($this->formatar($documento->load('arquivo')), 201);
    }

    public function update(Request $request, Documento $documento)
    {
        $documento = $this->doDono($request, $documento);

        $dados = $request->validate([
            'nome'  => 'sometimes|required|string|max:255',
            'corpo' => 'sometimes|nullable|string',
        ]);

        DB::transaction(function () use ($documento, $dados) {
            if (array_key_exists('corpo', $dados)) {
                $documento->update(['corpo' => $dados['corpo']]);
                $documento->arquivo->tamanho_kb = $this->tamanhoKb($dados['corpo'] ?? '');
            }
            if (array_key_exists('nome', $dados)) {
                $documento->arquivo->nome = $dados['nome'];
            }
            $documento->arquivo->save();
        });

        return $this->formatar($documento->fresh('arquivo'));
    }

    public function destroy(Request $request, Documento $documento)
    {
        $documento = $this->doDono($request, $documento);
        $documento->arquivo->delete(); // o cascade apaga o documento junto

        return response()->noContent();
    }

    // ---------- Auxiliares ----------

    private function doDono(Request $request, Documento $documento): Documento
    {
        $documento->load('arquivo');
        abort_unless($documento->arquivo->user_id === $request->user()->id, 404);

        return $documento;
    }

    private function tamanhoKb(string $texto): int
    {
        return max(1, (int) ceil(strlen($texto) / 1024));
    }

    private function formatar(Documento $d): array
    {
        return [
            'id'         => $d->id,
            'arquivo_id' => $d->arquivo_id,
            'nome'       => $d->arquivo->nome,
            'corpo'      => $d->corpo,
            'tamanho_kb' => $d->arquivo->tamanho_kb,
            'created_at' => $d->created_at,
            'updated_at' => $d->updated_at,
        ];
    }
}