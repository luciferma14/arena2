<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\SectorController;
use App\Models\Evento;
use App\Models\Precio;
use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    public function index()
    {
        // Llamadas en memoria a los controladores API (sin HTTP interno)
        $eventosResponse  = app(EventoController::class)->index();
        $sectoresResponse = app(SectorController::class)->index();

        $eventos  = $eventosResponse->getData(true)['data']  ?? [];
        $sectores = $sectoresResponse->getData(true)['data'] ?? [];

        return view('admin.index', compact('eventos', 'sectores'));
    }

    public function editEvento($id)
    {
        $response = app(EventoController::class)->show($id);
        $data     = $response->getData(true)['data'] ?? [];
        $evento   = $data['evento'] ?? [];

        return view('admin.edit-evento', compact('evento'));
    }

    public function createSector()
    {
        return view('admin.create-sector');
    }

    public function storeSector(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_base' => 'required|numeric|min:0',
        ]);

        // Normalizar el checkbox: si no viene en el form = false
        $request->merge(['activo' => $request->boolean('activo')]);

        $request->headers->set('Accept', 'application/json');

        try {
            $response = app(SectorController::class)->store($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $status = $response->getStatusCode();
        $data   = $response->getData(true);

        if ($status !== 201) {
            return back()->withErrors($data['errors'] ?? ['nombre' => 'Error al crear el sector.'])->withInput();
        }

        $sectorId   = $data['data']['id'];
        $precioBase = (float) $request->precio_base;

        // Acceso Eloquent directo: no existe endpoint API para crear precios en bloque.
        // Asignamos el sector a todos los eventos existentes con el precio indicado.
        $eventos = Evento::all();
        foreach ($eventos as $evento) {
            Precio::firstOrCreate(
                ['evento_id' => $evento->id, 'sector_id' => $sectorId],
                ['precio' => $precioBase, 'disponible' => true]
            );
        }

        return redirect()->route('admin.index')
            ->with('success', 'Sector creado y asignado a ' . $eventos->count() . ' eventos.');
    }

    public function editSector($id)
    {
        // El sector se carga via JS → GET /api/sectores/{id}
        // El formulario guarda via JS → PUT /api/admin/sectores/{id}
        return view('admin.edit-sector', ['sectorId' => $id]);
    }
}
