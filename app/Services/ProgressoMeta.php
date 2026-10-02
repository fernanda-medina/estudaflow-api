<?php

namespace App\Services;

class ProgressoMeta
{
    public function percentual(int $concluidas, int $total): int
    {
        if ($total === 0) {
            return 0;
        }

                return min(100, (int) round(($concluidas / $total) * 100));
    }
}