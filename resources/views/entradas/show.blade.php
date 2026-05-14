@extends('layouts.app')

@section('title', 'Entrada — Roig Arena')

@section('content')

<a href="{{ route('mis-entradas') }}" class="back-link">&#8592; Mis entradas</a>

@php
    $codigoQr = $entrada['codigo_qr'] ?? null;
    $valida   = $entrada['valida'] ?? false;
    $evento   = $entrada['evento']  ?? '-';
    $asiento  = $entrada['asiento'] ?? '-';
    $fecha    = $entrada['fecha']   ?? '-';
    $h        = $entrada['hora']    ?? '';
    $hora     = strlen($h) > 5 ? \Carbon\Carbon::parse($h)->format('H:i') : substr($h, 0, 5);
    $precio   = $entrada['precio']  ?? '-';
@endphp

<div style="max-width:560px;margin:0 auto">

    <div class="page-header">
        <p class="page-header-label">Entrada</p>
        <h1 class="page-header-title">{{ $evento }}</h1>
        <p class="page-header-meta">
            {{ $fecha }}
            @if($hora) &mdash; {{ $hora }} @endif
        </p>
    </div>

    {{-- QR --}}
    <div class="qr-block" style="margin-bottom:28px">
        <p class="qr-label">Codigo QR de acceso</p>
        @if($codigoQr)
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($codigoQr) }}"
                alt="Codigo QR"
                class="qr-image"
                width="200"
                height="200">
            <p class="qr-code-text">{{ $codigoQr }}</p>
        @else
            <div style="width:200px;height:200px;background:var(--surface);border:6px solid var(--border);border-radius:4px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center">
                <span style="color:var(--muted);font-size:.8rem">Sin QR</span>
            </div>
        @endif

        <div style="margin-top:16px">
            @if($valida)
                <span class="badge badge-success" style="font-size:.85rem;padding:6px 16px">Entrada valida</span>
            @else
                <span class="badge badge-danger" style="font-size:.85rem;padding:6px 16px">Entrada expirada</span>
            @endif
        </div>
    </div>

    {{-- Detalle --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Detalle</span>
        </div>
        <div class="table-wrap">
            <table class="table">
                <tbody>
                    <tr>
                        <td style="color:var(--muted);width:40%">Evento</td>
                        <td style="font-weight:600">{{ $evento }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--muted)">Asiento</td>
                        <td>{{ $asiento }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--muted)">Fecha</td>
                        <td>{{ $fecha }}</td>
                    </tr>
                    @if($hora)
                    <tr>
                        <td style="color:var(--muted)">Hora</td>
                        <td>{{ $hora }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color:var(--muted)">Precio pagado</td>
                        <td style="color:var(--gold);font-weight:700">{{ $precio }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
