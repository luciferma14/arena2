@extends('layouts.app')

@section('title', 'Nuevo sector — Roig Arena')

@section('content')

<a href="{{ route('admin.index') }}" class="back-link">&#8592; Panel admin</a>

<div class="form-card" style="max-width:520px">
    <h1 class="form-title">Nuevo sector</h1>

    <div id="form-msg"></div>

    <form id="create-form">
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del sector</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   required maxlength="255" placeholder="Ej: Pista, Platea Alta...">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripcion (opcional)</label>
            <textarea id="descripcion" name="descripcion" class="form-control"
                      rows="3" placeholder="Descripcion del sector..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" id="activo" name="activo" checked>
                Sector activo (disponible para asignar a eventos)
            </label>
        </div>

        <div style="display:flex;gap:12px;margin-top:32px">
            <button type="submit" class="btn btn-gold btn-lg" id="btn-crear">Crear sector</button>
            <a href="{{ route('admin.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('create-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-crear');
    btn.disabled    = true;
    btn.textContent = 'Creando...';

    const body = {
        nombre:      document.getElementById('nombre').value,
        descripcion: document.getElementById('descripcion').value || null,
        activo:      document.getElementById('activo').checked,
    };

    try {
        const res  = await fetch('/api/admin/sectores', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(body),
        });
        const json = await res.json();

        if (res.ok) {
            setMsg('success', 'Sector creado correctamente.');
            document.getElementById('create-form').reset();
            document.getElementById('activo').checked = true;
            setTimeout(() => { window.location.href = '{{ route('admin.index') }}'; }, 1200);
        } else {
            const errs = json.errors ? Object.values(json.errors).flat().join('<br>') : (json.message || 'Error al crear.');
            setMsg('error', errs);
        }
    } catch (err) {
        setMsg('error', 'Error de red. Intentalo de nuevo.');
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Crear sector';
    }
});

function setMsg(tipo, texto) {
    const cls = tipo === 'error' ? 'alert-error' : 'alert-success';
    document.getElementById('form-msg').innerHTML = `<div class="alert ${cls}" style="margin-bottom:20px">${texto}</div>`;
}
</script>
@endsection
