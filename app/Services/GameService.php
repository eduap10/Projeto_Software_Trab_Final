<?php

namespace App\Services;

use App\Models\User;
use App\Services\AchievementService;

class GameService
{
    private AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function awardPoints(User $user, int $points): void
    {
        $oldLevel = $user->level;
        $user->addExperience($points);

        // Verificar se subiu de nível
        if ($user->level > $oldLevel) {
            $this->handleLevelUp($user, $oldLevel);
        }

        // Verificar conquistas
        $this->achievementService->checkTaskAchievements($user);
    }

    private function handleLevelUp(User $user, int $oldLevel): void
    {
        $levelsGained = $user->level - $oldLevel;

        // Pode adicionar lógica adicional para quando o usuário sobe de nível
        // Ex: dar pontos bonus, desbloquear features, etc.

        // Pontos bonus por subir de nível
        $bonusPoints = $levelsGained * 25;
        $user->experience_points += $bonusPoints;
        $user->save();

        // Mensagem de nível
        if ($levelsGained == 1) {
            session()->flash('level_up', "🎉 Parabéns! Você atingiu o nível {$user->level}! (+{$bonusPoints} XP bonus)");
        } else {
            session()->flash('level_up', "🚀 Incrível! Você subiu {$levelsGained} níveis! Agora você está no nível {$user->level}! (+{$bonusPoints} XP bonus)");
        }
    }

    public function calculateLevelProgress(User $user): array
    {
        $currentLevelBase = ($user->level - 1) * 100;
        $nextLevelRequired = $user->level * 100;
        $progress = $user->experience_points - $currentLevelBase;
        $progressPercentage = ($progress / 100) * 100;

        return [
            'current_level' => $user->level,
            'progress' => max(0, $progress),
            'required_for_next' => 100,
            'percentage' => min(100, max(0, $progressPercentage)),
            'next_level_points' => max(0, $nextLevelRequired - $user->experience_points),
            'current_level_base' => $currentLevelBase,
            'next_level_required' => $nextLevelRequired
        ];
    }

    public function getUserStats(User $user): array
    {
        $tasks = $user->tasks();

        return [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'pending_tasks' => $tasks->where('status', 'pending')->count(),
            'cancelled_tasks' => $tasks->where('status', 'cancelled')->count(),
            'total_points' => $user->experience_points,
            'current_level' => $user->level,
            'achievements_count' => $user->achievements()->count(),
            'completion_rate' => $this->calculateCompletionRate($user),
            'level_progress' => $this->calculateLevelProgress($user)
        ];
    }

    private function calculateCompletionRate(User $user): float
    {
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->where('status', 'completed')->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
    }

    public function getLeaderboard(int $limit = 10): array
    {
        return User::orderBy('level', 'desc')
            ->orderBy('experience_points', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'level', 'experience_points'])
            ->map(function ($user, $index) {
                return [
                    'position' => $index + 1,
                    'name' => $user->name,
                    'level' => $user->level,
                    'points' => $user->experience_points
                ];
            })->toArray();
    }

    public function getRankingPosition(User $user): int
    {
        return User::where(function ($query) use ($user) {
            $query->where('level', '>', $user->level)
                ->orWhere(function ($q) use ($user) {
                    $q->where('level', $user->level)
                        ->where('experience_points', '>', $user->experience_points);
                });
        })->count() + 1;
    }
}
