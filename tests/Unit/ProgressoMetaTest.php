<?php

namespace Tests\Unit;

use App\Services\ProgressoMeta;
use PHPUnit\Framework\TestCase;

class ProgressoMetaTest extends TestCase
{
    public function test_calcula_percentual_de_progresso(): void
    {
        // Arrange
        $calculadora = new ProgressoMeta();

        // Act
        $resultado = $calculadora->percentual(concluidas: 3, total: 4);

        // Assert
        $this->assertSame(75, $resultado);
    }

    public function test_meta_sem_tarefas_tem_progresso_zero(): void
    {
        // Arrange
        $calculadora = new ProgressoMeta();

        // Act
        $resultado = $calculadora->percentual(concluidas: 0, total: 0);

        // Assert
        $this->assertSame(0, $resultado);
    }
            public function test_progresso_nunca_passa_de_100(): void
    {
        // Arrange
        $calculadora = new ProgressoMeta();

        // Act
        $resultado = $calculadora->percentual(concluidas: 5, total: 4);

        // Assert
        $this->assertSame(100, $resultado);
    }

}