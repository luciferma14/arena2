<?php

namespace App\Console\Commands;

use App\Services\LiberarReservasService;
use Illuminate\Console\Command;

class LiberarReservasExpiradas extends Command
{
    protected $signature = 'reservas:liberar';
    protected $description = 'Libera las reservas expiradas';

    public function handle(LiberarReservasService $service): void
    {
        $liberadas = $service->liberarExpiradas();
        $this->info("✅ Reservas liberadas: {$liberadas}");
    }
}
