<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MetaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_meta_valida_retorna_201(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson('/api/metas', [
            'nome'       => 'Estudar para QA',
            'disciplina' => 'Garantia de Qualidade',
            'prazo'      => now()->addDays(7)->toDateString(),
            'prioridade' => 'urgente',
        ]);

        // Assert
        $response->assertCreated();
        $this->assertDatabaseHas('metas', [
            'nome'    => 'Estudar para QA',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('metas', 1);
    }

    public function test_meta_sem_nome_retorna_422(): void
    {
        // Arrange
        Sanctum::actingAs(User::factory()->create());

        // Act
        $response = $this->postJson('/api/metas', [
            'disciplina' => 'Garantia de Qualidade',
            'prazo'      => now()->addDays(7)->toDateString(),
        ]);

        // Assert
        $response->assertUnprocessable()
                 ->assertJsonValidationErrors('nome');
        $this->assertDatabaseCount('metas', 0);
    }

    public function test_prioridade_invalida_retorna_422(): void
    {
        // Arrange
        Sanctum::actingAs(User::factory()->create());

        // Act
        $response = $this->postJson('/api/metas', [
            'nome'       => 'Estudar para QA',
            'disciplina' => 'Garantia de Qualidade',
            'prazo'      => now()->addDays(7)->toDateString(),
            'prioridade' => 'qualquer-coisa',
        ]);

        // Assert
        $response->assertUnprocessable()
                 ->assertJsonValidationErrors('prioridade');
    }

    public function test_sem_login_retorna_401(): void
    {
        // Act
        $response = $this->postJson('/api/metas', ['nome' => 'Teste']);

        // Assert
        $response->assertUnauthorized();
    }
}