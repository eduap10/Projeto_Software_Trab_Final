<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function getUserTasks(int $userId)
    {
        return Task::where('user_id', $userId)->get();
    }

    public function createForUser(array $data, int $userId): Task
    {
        return Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'pending',
            'points' => 10,
            'due_date' => $data['due_date'] ?? null,
            'user_id' => $userId,
        ]);
    }

    public function complete(Task $task): Task
    {
        $task->status = 'completed';
        $task->save();
        return $task;
    }
}
