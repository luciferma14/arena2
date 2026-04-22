<?php

namespace Database\Seeders;

use App\Models\Sector;
use App\Models\Asiento;
use Illuminate\Database\Seeder;

class AsientoSeeder extends Seeder
{
    public function run(): void
    {
        $sectores = Sector::all();
        $totalAsientos = 0;
        $now = now();

        foreach ($sectores as $sector) {
            $asientos = $this->generarAsientosPorSector($sector, $now);

            // Insertar en lotes de 1000
            foreach (array_chunk($asientos, 1000) as $lote) {
                Asiento::insert($lote);
            }

            $totalAsientos += count($asientos);
        }

        $this->command->info("✅ Asientos creados: {$totalAsientos}");
    }

    private function generarAsientosPorSector(Sector $sector, $now): array
    {
        $asientos = [];

        if (preg_match('/^Sector (10[1-9]|1[1-2][0-9]|30[1-9]|3[1-2][0-9])$/', $sector->nombre)) {
            for ($fila = 1; $fila <= 20; $fila++) {
                for ($numero = 1; $numero <= 15; $numero++) {
                    $asientos[] = [
                        'sector_id'  => $sector->id,
                        'fila'       => (string) $fila,
                        'numero'     => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif (str_starts_with($sector->nombre, 'Palco')) {
            for ($numero = 1; $numero <= 8; $numero++) {
                $asientos[] = [
                    'sector_id'  => $sector->id,
                    'fila'       => 'A',
                    'numero'     => $numero,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        } elseif ($sector->nombre === 'CLUB') {
            for ($fila = 1; $fila <= 10; $fila++) {
                for ($numero = 1; $numero <= 20; $numero++) {
                    $asientos[] = [
                        'sector_id'  => $sector->id,
                        'fila'       => (string) $fila,
                        'numero'     => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif ($sector->nombre === 'JOHNNIE WALKER') {
            for ($fila = 1; $fila <= 8; $fila++) {
                for ($numero = 1; $numero <= 15; $numero++) {
                    $asientos[] = [
                        'sector_id'  => $sector->id,
                        'fila'       => (string) $fila,
                        'numero'     => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif ($sector->nombre === 'PISTA') {
            for ($fila = 1; $fila <= 30; $fila++) {
                for ($numero = 1; $numero <= 25; $numero++) {
                    $asientos[] = [
                        'sector_id'  => $sector->id,
                        'fila'       => (string) $fila,
                        'numero'     => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif ($sector->nombre === 'FRONT STAGE') {
            for ($fila = 1; $fila <= 5; $fila++) {
                for ($numero = 1; $numero <= 30; $numero++) {
                    $asientos[] = [
                        'sector_id'  => $sector->id,
                        'fila'       => (string) $fila,
                        'numero'     => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        return $asientos;
    }
}
