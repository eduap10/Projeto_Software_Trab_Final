@extends('layouts.app')

@section('title', 'Minhas Tarefas')

@section('content')


<div class="mb-4">
    <h2><i class="bi bi-speedometer2"></i> Meu Progresso</h2>
    <div class="progress">
        <div
            class="progress-bar bg-success"
            role="progressbar"
            style="width: {{ $progress }}%;"
            aria-valuenow="{{ $progress ?? 0 }}"
            aria-valuemin="0"
            aria-valuemax="100">
            {{ $progress ?? 0 }}%
        </div>
    </div>
</div>

<div class="row text-center mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Tarefas Concluídas</h6>
                <span class="text-success fw-bold">{{ $completed ?? 0 }}</span>
                <div class="progress mt-2">
                    <div class="progress-bar bg-success"
                        role="progressbar"
                        style="width: {{ $total > 0 ? round(($completed / $total) * 100) : 0 }}%;"
                        aria-valuenow="{{ $total > 0 ? round(($completed / $total) * 100) : 0 }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        {{ $total > 0 ? round(($completed / $total) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Tarefas em Andamento</h6>
                <span class="text-primary fw-bold">{{ $inProgress ?? 0 }}</span>
                <div class="progress mt-2">
                    <div class="progress-bar bg-progress"
                        role="progressbar"
                        style="width: {{ $total > 0 ? round(($inProgress / $total) * 100) : 0 }}%;"
                        aria-valuenow="{{ $total > 0 ? round(($inProgress / $total) * 100) : 0 }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        {{ $total > 0 ? round(($inProgress / $total) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Tarefas Canceladas</h6>
                <span class="text-danger fw-bold">{{ $canceled ?? 0 }}</span>
                <div class="progress mt-2">
                    <div class="progress-bar bg-canceled"
                        role="progressbar"
                        style="width: {{ $total > 0 ? round(($canceled / $total) * 100) : 0 }}%;"
                        aria-valuenow="{{ $total > 0 ? round(($canceled / $total) * 100) : 0 }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        {{ $total > 0 ? round(($canceled / $total) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Tarefas Pausadas</h6>
                <span class="text-warning fw-bold">{{ $paused ?? 0 }}</span>
                <div class="progress mt-2">
                    <div class="progress-bar bg-paused"
                        role="progressbar"
                        style="width: {{ $total > 0 ? round(($paused / $total) * 100) : 0 }}%;"
                        aria-valuenow="{{ $total > 0 ? round(($paused / $total) * 100) : 0 }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        {{ $total > 0 ? round(($paused / $total) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-list-task"></i> Minhas Tarefas</h2>
    <!-- Botão abre modal -->
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTaskModal">
        <i class="bi bi-plus-circle"></i> Nova Tarefa
    </button>
</div>

<!-- {{-- Alertas --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif -->

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    @forelse ($tasks as $task)
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm task-card">
            <div class="card-body">
                <h5 class="card-title">{{ $task->title }}</h5>
                <p class="card-text text-muted">{{ Str::limit($task->description, 80) }}</p>

                <span class="badge 
                        @if($task->priority == 'high') bg-danger
                        @elseif($task->priority == 'medium') bg-warning
                        @else bg-success @endif">
                    {{ ucfirst($task->priority) }}
                </span>

                <p class="mt-2">
                    <span class="badge bg-secondary">{{ ucfirst($task->status) }}</span>
                </p>

                <div class="mt-3 d-flex justify-content-between">
                    <!-- Botão editar -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#editTaskModal{{ $task->id }}">
                        <i class="bi bi-pencil"></i> Editar
                    </button>

                    <!-- Form excluir -->
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Tarefa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="title" value="{{ $task->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="description">{{ $task->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prioridade</label>
                            <select class="form-select" name="priority">
                                <option value="low" {{ $task->priority=='low'?'selected':'' }}>Baixa</option>
                                <option value="medium" {{ $task->priority=='medium'?'selected':'' }}>Média</option>
                                <option value="high" {{ $task->priority=='high'?'selected':'' }}>Alta</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="pending" {{ $task->status=='pending'?'selected':'' }}>Em Andamento</option>
                                <option value="completed" {{ $task->status=='completed'?'selected':'' }}>Concluída</option>
                                <option value="canceled" {{ $task->status=='canceled'?'selected':'' }}>Cancelada</option>
                                <option value="paused" {{ $task->status=='paused'?'selected':'' }}>Pausada</option>
                            </select>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p>Nenhuma tarefa cadastrada ainda.</p>
    @endforelse
</div>

<!-- Modal Criar -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova Tarefa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridade</label>
                        <select class="form-select" name="priority">
                            <option value="low">Baixa</option>
                            <option value="medium">Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="pending">Em Andamento</option>
                            <option value="completed">Concluída</option>
                            <option value="canceled">Cancelada</option>
                            <option value="paused">Pausada</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection