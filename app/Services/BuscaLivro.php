<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BuscaLivro
{
    public function porIsbn(string $isbn): ?string
    {
        $resposta = Http::get("https://openlibrary.org/isbn/{$isbn}.json");

        if ($resposta->failed()) {
            return null;
        }

        return $resposta->json('title');
    }
}