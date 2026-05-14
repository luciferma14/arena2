<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EntradaController;
use Illuminate\Http\Request;

class EntradaWebController extends Controller
{
    public function index(Request $request)
    {
        // Llamada en memoria al controlador API (sin HTTP interno)
        $response = app(EntradaController::class)->index($request);
        $entradas = $response->getData(true)['data'] ?? [];

        return view('entradas.index', compact('entradas'));
    }

    public function show($id, Request $request)
    {
        // Obtenemos datos planos del listado (evento, asiento, precio, fecha, hora)
        $indexResponse = app(EntradaController::class)->index($request);
        $todas         = $indexResponse->getData(true)['data'] ?? [];
        $entradaBase   = collect($todas)->firstWhere('id', (int) $id);

        if (!$entradaBase) {
            abort(404);
        }

        // Obtenemos datos completos: codigo_qr e informacion adicional
        $showResponse  = app(EntradaController::class)->show($id);
        $entradaDetalle = $showResponse->getData(true)['data'] ?? [];

        // Combinamos ambas fuentes
        $entrada = array_merge((array) $entradaBase, (array) $entradaDetalle);

        return view('entradas.show', compact('entrada'));
    }
}
