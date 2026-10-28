<?php

namespace Database\Seeders;

use App\Models\Livro;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class LivroSeeder extends Seeder
{
    public function run(): void
    {
        $livros = [
            ['Cálculo — James Stewart', 'James Stewart', 'matematica', '📘', ['Cálculo', 'Análise', 'Limites']],
            ['Introdução à Computação', 'Forouzan & Mosharraf', 'computacao', '💻', ['Computação', 'Algoritmos']],
            ['Fundamentos de Física I', 'Halliday & Resnick', 'fisica', '⚡', ['Mecânica', 'Física']],
            ['Algoritmos — CLRS', 'Cormen, Leiserson et al.', 'computacao', '🔵', ['Algoritmos', 'Estruturas de Dados']],
            ['Álgebra Linear', 'Boldrini, Costa et al.', 'matematica', '📗', ['Álgebra', 'Matrizes', 'Vetores']],
            ['Sistemas Operacionais', 'Tanenbaum', 'computacao', '🖥️', ['SO', 'Processos', 'Memória']],
            ['Física para Cientistas II', 'Serway & Jewett', 'fisica', '🔬', ['Eletromagnetismo', 'Óptica']],
            ['Compiladores — Aho', 'Aho, Lam, Sethi, Ullman', 'linguagens', '📙', ['Compiladores', 'Parsing']],
        ];

        foreach ($livros as [$titulo, $autor, $area, $icon, $tags]) {
            $livro = Livro::firstOrCreate(
                ['titulo' => $titulo],
                ['autor' => $autor, 'area' => $area, 'icon' => $icon]
            );

            $ids = collect($tags)
                ->map(fn ($nome) => Tag::firstOrCreate(['nome' => $nome])->id)
                ->all();

            $livro->tags()->sync($ids);
        }
    }
}