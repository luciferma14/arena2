@extends('layouts.app')

@section('title', 'Mis entradas — Roig Arena')

@section('content')

<div class="page-header">
    <p class="page-header-label">Mi cuenta</p>
    <h1 class="page-header-title">Mis entradas</h1>
</div>

@if(count($entradas) === 0)
    <div class="state-msg">
        <p class="state-msg-title">Sin entradas</p>
        <p>Cuando compres entradas apareceran aqui.</p>
        <a href="{{ route('eventos.index') }}" class="btn btn-outline" style="margin-top:20px">Ver eventos</a>
    </div>
@else
    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Asiento</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entradas as $e)
                    <tr>
                        <td style="font-weight:600">{{ $e['evento'] ?? '-' }}</td>
                        <td>{{ $e['fecha'] ?? '-' }}</td>
                        <td>
                            @php
                                $h = $e['hora'] ?? '';
                                echo strlen($h) > 5 ? \Carbon\Carbon::parse($h)->format('H:i') : substr($h, 0, 5);
                            @endphp
                        </td>
                        <td>{{ $e['asiento'] ?? '-' }}</td>
                        <td>{{ $e['precio'] ?? '-' }}</td>
                        <td>
                            @if($e['valida'] ?? false)
                                <span class="badge badge-success">Valida</span>
                            @else
                                <span class="badge badge-danger">Expirada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('entradas.show', $e['id']) }}" class="btn btn-outline btn-sm">Ver QR</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
