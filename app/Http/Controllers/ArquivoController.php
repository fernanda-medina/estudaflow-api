<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;

class ArquivoController extends Controller
{
    public function index(Request $request)
    {
        return Arquivo::with('documento:id,arquivo_id')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get(['id', 'nome', 'tipo', 'tamanho_kb', 'created_at']);
    }

    public function destroy(Request $request, Arquivo $arquivo)
    {
        abort_unless($arquivo->user_id === $request->user()->id, 404);
        $arquivo->delete();

        return response()->noContent();
    }
}