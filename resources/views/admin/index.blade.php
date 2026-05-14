@extends('layouts.app')

@section('title', 'Admin — Roig Arena')

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px">
    <div>
        <p class="page-header-label">Administración</p>
        <h1 class="page-header-title">Panel</h1>
    </div>
    <a href="{{ route('eventos.create') }}" class="btn btn-gold">Nuevo evento</a>
</div>

{{-- ── EVENTOS ──────────────────────────────────────────────── --}}
<div class="panel" style="margin-bottom:40px">
    <div class="panel-header">
        <span class="panel-title">Eventos</span>
        <a href="{{ route('eventos.create') }}" class="btn btn-outline btn-sm">Nuevo evento</a>
    </div>

    @if(count($eventos) === 0)
        <div class="panel-body state-msg">Sin eventos registrados.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventos as $ev)
                    @php
                        $fechaFmt = $ev['fecha'] ?? '';
                        try { $fechaFmt = \Carbon\Carbon::parse($ev['fecha'])->format('d/m/Y'); } catch(\Exception $ex) {}
                    @endphp
                    <tr>
                        <td style="font-weight:600">{{ $ev['nombre'] ?? '-' }}</td>
                        <td>{{ $fechaFmt }}</td>
                        <td>{{ substr($ev['hora'] ?? '', 0, 5) }}</td>
                        <td style="text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.eventos.edit', $ev['id']) }}" class="btn btn-ghost btn-sm" style="margin-right:6px">Editar</a>
                            <button class="btn btn-danger btn-sm" onclick="eliminarEvento({{ $ev['id'] }}, '{{ addslashes($ev['nombre'] ?? '') }}')">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── SECTORES ─────────────────────────────────────────────── --}}
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Sectores</span>
        <a href="{{ route('admin.sectores.create') }}" class="btn btn-outline btn-sm">Nuevo sector</a>
    </div>

    @if(count($sectores) === 0)
        <div class="panel-body state-msg">Sin sectores registrados.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sectores as $s)
                    <tr>
                        <td style="font-weight:600">{{ $s['nombre'] ?? '-' }}</td>
                        <td style="color:var(--muted)">{{ $s['descripcion'] ?? '-' }}</td>
                        <td>
                            @if($s['activo'] ?? false)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-muted">Inactivo</span>
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.sectores.edit', $s['id']) }}" class="btn btn-ghost btn-sm" style="margin-right:6px">Editar</a>
                            <button class="btn btn-danger btn-sm" onclick="eliminarSector({{ $s['id'] }}, '{{ addslashes($s['nombre'] ?? '') }}')">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div id="admin-msg"></div>

@endsection

@section('scripts')
<script>
async function eliminarEvento(id, nombre) {
    if (!confirm('¿Eliminar el evento "' + nombre + '"? Esta acción no se puede deshacer.')) return;

    const res  = await fetch(`/api/admin/eventos/${id}`, { method: 'DELETE' });
    const json = await res.json();

    if (res.ok) {
        location.reload();
    } else {
        mostrarMsg('error', json.error || 'No se pudo eliminar el evento.');
    }
}

async function eliminarSector(id, nombre) {
    if (!confirm('¿Eliminar el sector "' + nombre + '"? Esta acción no se puede deshacer.')) return;

    const res  = await fetch(`/api/admin/sectores/${id}`, { method: 'DELETE' });
    const json = await res.json();

    if (res.ok) {
        location.reload();
    } else {
        mostrarMsg('error', json.error || 'No se pudo eliminar el sector.');
    }
}

function mostrarMsg(tipo, texto) {
    const cls = tipo === 'error' ? 'alert-error' : 'alert-success';
    document.getElementById('admin-msg').innerHTML =
        `<div class="alert ${cls}" style="margin-top:20px">${texto}</div>`;
    window.scrollTo(0, document.body.scrollHeight);
}
</script>
@endsection
