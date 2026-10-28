<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LivroController extends Controller
{
    private const AREAS = 'matematica,computacao,fisica,linguagens';

    public function index(Request $request)
    {
        $naLista = $this->idsNaLista($request);

        return Livro::with('tags')->orderBy('titulo')->get()
            ->map(fn (Livro $livro) => $this->formatar($livro, $naLista));
    }

    public function show(Request $request, Livro $livro)
    {
        return $this->formatar($livro->load('tags'), $this->idsNaLista($request));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor'  => 'required|string|max:255',
            'area'   => 'required|in:' . self::AREAS,
            'icon'   => 'required|string|max:10',
            'tags'   => 'nullable|array',
            'tags.*' => 'string|max:100',
        ]);

        $livro = Livro::create(collect($dados)->except('tags')->all());
        $this->sincronizarTags($livro, $dados['tags'] ?? []);

        return response()->json($this->formatar($livro->load('tags'), []), 201);
    }

    public function update(Request $request, Livro $livro)
    {
        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'autor'  => 'sometimes|required|string|max:255',
            'area'   => 'sometimes|required|in:' . self::AREAS,
            'icon'   => 'sometimes|required|string|max:10',
            'tags'   => 'sometimes|array',
            'tags.*' => 'string|max:100',
        ]);

        $livro->update(collect($dados)->except('tags')->all());

        if (array_key_exists('tags', $dados)) {
            $this->sincronizarTags($livro, $dados['tags']);
        }

        return $this->formatar($livro->load('tags'), $this->idsNaLista($request));
    }

    public function destroy(Livro $livro)
    {
        $livro->tags()->detach();
        $livro->delete();

        return response()->noContent();
    }

    

    public function lista(Request $request)
    {
        $userId = $request->user()->id;

        return Livro::with('tags')
            ->whereHas('leitores', fn ($q) => $q->where('users.id', $userId))
            ->get()
            ->map(fn (Livro $livro) => $this->formatar($livro, [$livro->id]));
    }

    public function adicionarNaLista(Request $request, Livro $livro)
    {
        $livro->leitores()->syncWithoutDetaching([$request->user()->id]);

        return response()->json(['message' => 'Livro adicionado à lista de leitura'], 201);
    }

    public function removerDaLista(Request $request, Livro $livro)
    {
        $livro->leitores()->detach($request->user()->id);

        return response()->noContent();
    }

    private function sincronizarTags(Livro $livro, array $nomes): void
    {
        $ids = collect($nomes)
            ->map(fn ($nome) => trim($nome))
            ->filter()
            ->unique()
            ->map(fn ($nome) => Tag::firstOrCreate(['nome' => $nome])->id)
            ->all();

        $livro->tags()->sync($ids);
    }

    private function idsNaLista(Request $request): array
    {
        return DB::table('lista_leitura')
            ->where('user_id', $request->user()->id)
            ->pluck('livro_id')
            ->all();
    }

    private function formatar(Livro $livro, array $naLista): array
    {
        return [
            'id'      => $livro->id,
            'titulo'  => $livro->titulo,
            'autor'   => $livro->autor,
            'area'    => $livro->area,
            'icon'    => $livro->icon,
            'tags'    => $livro->tags->pluck('nome')->values(),
            'na_lista' => in_array($livro->id, $naLista),
        ];
    }
}