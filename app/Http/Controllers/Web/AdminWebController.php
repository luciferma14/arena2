<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\SectorController;
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
        // El formulario guarda via JS → POST /api/admin/sectores
        return view('admin.create-sector');
    }

    public function editSector($id)
    {
        // El sector se carga via JS → GET /api/sectores/{id}
        // El formulario guarda via JS → PUT /api/admin/sectores/{id}
        return view('admin.edit-sector', ['sectorId' => $id]);
    }
}
