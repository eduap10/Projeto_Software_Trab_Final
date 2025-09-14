<?php

namespace App\Factories;

use App\Models\Task;

use App\Contracts\TaskFactoryInterface;

class TaskFactory implements TaskFactoryInterface
{
    public function create(array $data, int $userId): Task
    {
        $points = $this->calculatePoints($data['priority'] ?? 'medium');

        return Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'pending',
            'points' => $points,
            'due_date' => $data['due_date'] ?? null,
            'user_id' => $userId,
        ]);
    }

    private function calculatePoints(string $priority): int
    {
        return match ($priority) {
            'high' => 30,
            'medium' => 20,
            'low' => 10,
            default => 15,
        };
    }
}
