@extends('layouts.app')

@section('title', 'Editar evento — Roig Arena')

@section('content')

<a href="{{ route('admin.index') }}" class="back-link">&#8592; Panel admin</a>

<div class="form-card" style="max-width:640px">
    <h1 class="form-title">Editar evento</h1>

    <div id="form-msg"></div>

    <form id="edit-form">
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del evento</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="{{ $evento['nombre'] ?? '' }}" required maxlength="255">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion_corta">Descripcion corta</label>
            <input type="text" id="descripcion_corta" name="descripcion_corta" class="form-control"
                   value="{{ $evento['descripcion_corta'] ?? '' }}" required maxlength="255">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion_larga">Descripcion completa</label>
            <textarea id="descripcion_larga" name="descripcion_larga" class="form-control"
                      rows="5" required>{{ $evento['descripcion_larga'] ?? '' }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="poster_url">URL del poster (opcional)</label>
            <input type="url" id="poster_url" name="poster_url" class="form-control"
                   value="{{ $evento['poster_url'] ?? '' }}" placeholder="https://...">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label class="form-label" for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" class="form-control"
                       value="{{ substr($evento['fecha'] ?? '', 0, 10) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="hora">Hora</label>
                <input type="time" id="hora" name="hora" class="form-control"
                       value="{{ substr($evento['hora'] ?? '', 0, 5) }}" required>
            </div>
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
const eventoId = {{ $evento['id'] ?? 0 }};

document.getElementById('edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-guardar');
    btn.disabled    = true;
    btn.textContent = 'Guardando...';

    const body = {
        nombre:            document.getElementById('nombre').value,
        descripcion_corta: document.getElementById('descripcion_corta').value,
        descripcion_larga: document.getElementById('descripcion_larga').value,
        poster_url:        document.getElementById('poster_url').value || null,
        fecha:             document.getElementById('fecha').value,
        hora:              document.getElementById('hora').value,
    };

    try {
        const res  = await fetch(`/api/admin/eventos/${eventoId}`, {
            method:  'PUT',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(body),
        });
        const json = await res.json();

        if (res.ok) {
            setMsg('success', 'Evento actualizado correctamente.');
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
    window.scrollTo(0, 0);
}
</script>
@endsection
