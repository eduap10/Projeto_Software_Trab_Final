<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;

        $total      = Task::where('user_id', $userId)->count();
        $completed  = Task::where('user_id', $userId)->where('status', 'completed')->count();
        $inProgress = Task::where('user_id', $userId)->where('status', 'pending')->count();
        $canceled   = Task::where('user_id', $userId)->where('status', 'canceled')->count();
        $paused     = Task::where('user_id', $userId)->where('status', 'paused')->count();

        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        // ⚠️ Se progresso chegou a 100%, sobe nível (update direto no banco) e recarrega o usuário
        // if ($progress === 100) {
        //     DB::table('users')->where('id', $userId)->increment('level'); // atualiza na base
        //     $user = User::find($userId); // recarrega o modelo (navbar mostra o level atualizado)
        //     $progress = 0;               // zera a barra de progresso exibida
        // }

        $tasks = Task::where('user_id', $userId)->get();

        return view('tasks.index', compact(
            'tasks',
            'total',
            'completed',
            'inProgress',
            'canceled',
            'paused',
            'progress',
            'user'
        ));
    }


    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,completed,canceled,paused',
        ]);

        $task = $this->taskService->createForUser($validated, Auth::id());


        return redirect()->route('tasks.index')->with('success', 'Tarefa criada!');
    }

    public function complete(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $this->taskService->complete($task);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarefa concluída!');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,completed,canceled,paused',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarefa atualizada!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tarefa excluída!');
    }
}
