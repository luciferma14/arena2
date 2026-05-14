@extends('layouts.app')

@section('title', 'Editar sector — Roig Arena')

@section('content')

<a href="{{ route('admin.index') }}" class="back-link">&#8592; Panel admin</a>

<div class="form-card" style="max-width:520px">
    <h1 class="form-title">Editar sector</h1>

    <div id="form-msg">
        <div class="alert alert-info">Cargando datos del sector...</div>
    </div>

    <form id="edit-form" style="display:none">
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del sector</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   required maxlength="255">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripcion (opcional)</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" id="activo" name="activo">
                Sector activo
            </label>
        </div>

        <div style="display:flex;gap:12px;margin-top:32px">
            <button type="submit" class="btn btn-gold btn-lg" id="btn-guardar">Guardar cambios</button>
            <a href="{{ route('admin.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
const sectorId = {{ $sectorId }};

// Cargar datos del sector al inicio
async function cargarSector() {
    try {
        const res  = await fetch(`/api/sectores/${sectorId}`);
        const json = await res.json();
        const s    = json.data;

        document.getElementById('nombre').value      = s.nombre || '';
        document.getElementById('descripcion').value = s.descripcion || '';
        document.getElementById('activo').checked    = !!s.activo;

        document.getElementById('form-msg').innerHTML = '';
        document.getElementById('edit-form').style.display = 'block';
    } catch (err) {
        setMsg('error', 'Error al cargar el sector.');
    }
}

document.getElementById('edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-guardar');
    btn.disabled    = true;
    btn.textContent = 'Guardando...';

    const body = {
        nombre:      document.getElementById('nombre').value,
        descripcion: document.getElementById('descripcion').value || null,
        activo:      document.getElementById('activo').checked,
    };

    try {
        const res  = await fetch(`/api/admin/sectores/${sectorId}`, {
            method:  'PUT',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(body),
        });
        const json = await res.json();

        if (res.ok) {
            setMsg('success', 'Sector actualizado correctamente.');
            setTimeout(() => { window.location.href = '{{ route('admin.index') }}'; }, 1200);
        } else {
            const errs = json.errors ? Object.values(json.errors).flat().join('<br>') : (json.message || 'Error al guardar.');
            setMsg('error', errs);
        }
    } catch (err) {
        setMsg('error', 'Error de red. Intentalo de nuevo.');
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Guardar cambios';
    }
});

function setMsg(tipo, texto) {
    const cls = tipo === 'error' ? 'alert-error' : 'alert-success';
    document.getElementById('form-msg').innerHTML = `<div class="alert ${cls}" style="margin-bottom:20px">${texto}</div>`;
}

cargarSector();
</script>
@endsection
