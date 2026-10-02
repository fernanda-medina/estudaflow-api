<?php

namespace Tests\Feature;

use App\Services\BuscaLivro;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BuscaLivroTest extends TestCase
{
    public function test_busca_titulo_sem_chamar_a_internet(): void
    {
        // Arrange
        Http::preventStrayRequests();
        Http::fake([
            'openlibrary.org/*' => Http::response(['title' => 'Clean Code'], 200),
        ]);

        // Act
        $titulo = (new BuscaLivro())->porIsbn('9780132350884');

        // Assert
        $this->assertSame('Clean Code', $titulo);
        Http::assertSentCount(1);
    }

    public function test_retorna_null_quando_a_api_falha(): void
    {
        // Arrange
        Http::preventStrayRequests();
        Http::fake([
            'openlibrary.org/*' => Http::response([], 404),
        ]);

        // Act
        $titulo = (new BuscaLivro())->porIsbn('000');

        // Asserts
        $this->assertNull($titulo);
    }
}