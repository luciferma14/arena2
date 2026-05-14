<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EventoController;
use App\Models\Sector;
use App\Models\Precio;
use Illuminate\Http\Request;

class EventoWebController extends Controller
{
    public function index()
    {
        // Los datos se cargan via JS desde /api/eventos
        return view('eventos.index');
    }

    public function show($id)
    {
        // Los datos se cargan via JS desde /api/eventos/{id}
        return view('eventos.show', ['id' => $id]);
    }

    public function create()
    {
        return view('eventos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'precio_base' => 'required|numeric|min:0',
        ]);

        // Forzamos JSON para obtener respuesta estructurada del EventoController
        $request->headers->set('Accept', 'application/json');

        try {
            $response = app(EventoController::class)->store($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $status = $response->getStatusCode();
        $data   = $response->getData(true);

        if ($status !== 201) {
            return back()->withErrors($data['errors'] ?? ['general' => 'Error al crear el evento.'])->withInput();
        }

        $eventoId   = $data['data']['id'];
        $precioBase = (float) $request->precio_base;

        // Acceso Eloquent directo: no existe endpoint API para crear precios en bloque.
        // Asignamos precio_base a todos los sectores activos.
        $sectores = Sector::where('activo', true)->get();
        foreach ($sectores as $sector) {
            Precio::create([
                'evento_id'  => $eventoId,
                'sector_id'  => $sector->id,
                'precio'     => $precioBase,
                'disponible' => true,
            ]);
        }

        return redirect()->route('admin.index')
            ->with('success', 'Evento creado correctamente con ' . $sectores->count() . ' sectores.');
    }
}
