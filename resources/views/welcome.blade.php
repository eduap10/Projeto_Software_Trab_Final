@extends('layouts.app')
@section('title', 'Bem-vindo ao Vortex - Gaming Task')

@section('content')
<div class="text-center mt-5">
    <h1 class="mb-4">🎮 Vortex - Gaming Task</h1>
    <p class="lead">Gerencie suas tarefas de forma divertida e gamificada!</p>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </a>
        <a href="{{ route('register') }}" class="btn btn-success btn-lg">
            <i class="bi bi-person-plus"></i> Registrar
        </a>
    </div>
</div>
@endsection