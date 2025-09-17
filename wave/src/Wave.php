<?php

declare(strict_types=1);

namespace Wave;

class Wave
{
    public function routes(): void
    {
        require __DIR__.'/../routes/web.php';
    }

    public function api(): void
    {
        require __DIR__.'/../routes/api.php';
    }
}
