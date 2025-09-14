@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </div>

                <div class="card-body">
                    <h4>Bem-vindo, {{ auth()->user()->name }}!</h4>
                    <p>Você está logado no <strong>Vortex - Gaming Task</strong>.</p>

                    <div class="mt-3">
                        <a href="{{ route('tasks.index') }}" class="btn btn-success">
                            <i class="bi bi-list-task"></i> Ver Tarefas
                        </a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection