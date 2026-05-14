<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [
            [
                'nombre' => 'Concierto Rock 2026',
                'descripcion_corta' => 'El mejor concierto de rock del año',
                'descripcion_larga' => 'Disfruta de una noche inolvidable con las mejores bandas de rock internacional.',
                'poster_url' => 'https://raw.githubusercontent.com/luciferma14/arena2/main/public/images/conciertoRock.jpg',
                'fecha' => '2026-06-15',
                'hora' => '20:00',
            ],
            [
                'nombre' => 'Final Copa del Rey',
                'descripcion_corta' => 'Gran final de la Copa del Rey',
                'descripcion_larga' => 'Vive la emoción de la final de la Copa del Rey en directo.',
                'poster_url' => 'https://raw.githubusercontent.com/luciferma14/arena2/main/public/images/copaRey.webp',
                'fecha' => '2026-07-20',
                'hora' => '21:00',
            ],
            [
                'nombre' => 'Festival Electrónica',
                'descripcion_corta' => 'Los mejores DJs del mundo',
                'descripcion_larga' => 'Festival de música electrónica con los DJs más reconocidos a nivel mundial.',
                'poster_url' => 'https://raw.githubusercontent.com/luciferma14/arena2/main/public/images/electronica.jpg',
                'fecha' => '2026-08-10',
                'hora' => '19:00',
            ],
            [
                'nombre' => 'Obra de Teatro Clásico',
                'descripcion_corta' => 'Teatro clásico español',
                'descripcion_larga' => 'Representación de una obra clásica del teatro español con los mejores actores del país.',
                'poster_url' => 'https://raw.githubusercontent.com/luciferma14/arena2/main/public/images/teatro.jpg',
                'fecha' => '2026-09-05',
                'hora' => '18:30',
            ],
        ];

        foreach ($eventos as $evento) {
            Evento::create($evento);
        }

        $this->command->info('✅ Eventos creados: ' . count($eventos));
    }
}
