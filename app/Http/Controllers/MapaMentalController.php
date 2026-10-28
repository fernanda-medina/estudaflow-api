<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\MapaMental;
use App\Models\MapaNo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapaMentalController extends Controller
{
    private const REGRAS_NOS = [
        'nos.*.ref'        => 'required',
        'nos.*.parent_ref' => 'nullable',
        'nos.*.label'      => 'required|string|max:255',
        'nos.*.x'          => 'required|numeric',
        'nos.*.y'          => 'required|numeric',
        'nos.*.cor'        => 'nullable|string|max:20',
    ];

    public function index(Request $request)
    {
        return MapaMental::with(['arquivo', 'nos'])
            ->whereHas('arquivo', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->get()
            ->map(fn (MapaMental $m) => $this->formatar($m));
    }

    public function show(Request $request, MapaMental $mapa)
    {
        return $this->formatar($this->doDono($request, $mapa));
    }

    public function store(Request $request)
    {
        $dados = $request->validate(array_merge(self::REGRAS_NOS, [
            'nome' => 'required|string|max:255',
            'nos'  => 'nullable|array',
        ]));
        $nos = $dados['nos'] ?? [];

        $mapa = DB::transaction(function () use ($request, $dados, $nos) {
            $arquivo = Arquivo::create([
                'user_id'    => $request->user()->id,
                'nome'       => $dados['nome'],
                'tipo'       => 'mapa',
                'tamanho_kb' => $this->tamanhoKb($nos),
            ]);

            $mapa = $arquivo->mapaMental()->create();
            $this->salvarNos($mapa, $nos);

            return $mapa;
        });

        return response()->json($this->formatar($mapa->load(['arquivo', 'nos'])), 201);
    }

    public function update(Request $request, MapaMental $mapa)
    {
        $mapa = $this->doDono($request, $mapa);

        $dados = $request->validate(array_merge(self::REGRAS_NOS, [
            'nome' => 'sometimes|required|string|max:255',
            'nos'  => 'sometimes|nullable|array',
        ]));

        DB::transaction(function () use ($mapa, $dados) {
            if (array_key_exists('nos', $dados)) {
                $nos = $dados['nos'] ?? [];
                $this->salvarNos($mapa, $nos);
                $mapa->arquivo->tamanho_kb = $this->tamanhoKb($nos);
            }
            if (array_key_exists('nome', $dados)) {
                $mapa->arquivo->nome = $dados['nome'];
            }
            $mapa->arquivo->save();
        });

        return $this->formatar($mapa->fresh(['arquivo', 'nos']));
    }

    public function destroy(Request $request, MapaMental $mapa)
    {
        $mapa = $this->doDono($request, $mapa);
        $mapa->arquivo->delete(); // o cascade apaga o mapa e os nós

        return response()->noContent();
    }

    // ---------- Auxiliares ----------

    private function salvarNos(MapaMental $mapa, array $nos): void
    {
        $mapa->nos()->delete();

        // 1ª passada: cria os nós e guarda ref (id provisório) => id real
        $ids = [];
        foreach ($nos as $n) {
            $no = $mapa->nos()->create([
                'label' => $n['label'],
                'x'     => (int) round($n['x']),
                'y'     => (int) round($n['y']),
                'cor'   => $n['cor'] ?? null,
            ]);
            $ids[(string) $n['ref']] = $no->id;
        }

        // 2ª passada: liga cada nó ao pai (se o pai existir)
        foreach ($nos as $n) {
            $pai = $n['parent_ref'] ?? null;
            if ($pai !== null && isset($ids[(string) $pai])) {
                MapaNo::where('id', $ids[(string) $n['ref']])
                    ->update(['parent_id' => $ids[(string) $pai]]);
            }
        }
    }

    private function doDono(Request $request, MapaMental $mapa): MapaMental
    {
        $mapa->load('arquivo');
        abort_unless($mapa->arquivo->user_id === $request->user()->id, 404);

        return $mapa;
    }

    private function tamanhoKb(array $nos): int
    {
        return max(1, (int) ceil(strlen(json_encode($nos)) / 1024));
    }

    private function formatar(MapaMental $m): array
    {
        return [
            'id'         => $m->id,
            'arquivo_id' => $m->arquivo_id,
            'nome'       => $m->arquivo->nome,
            'tamanho_kb' => $m->arquivo->tamanho_kb,
            'nos'        => $m->nos->map(fn ($n) => [
                'id'        => $n->id,
                'parent_id' => $n->parent_id,
                'label'     => $n->label,
                'x'         => $n->x,
                'y'         => $n->y,
                'cor'       => $n->cor,
            ])->values(),
            'created_at' => $m->created_at,
            'updated_at' => $m->updated_at,
        ];
    }
}