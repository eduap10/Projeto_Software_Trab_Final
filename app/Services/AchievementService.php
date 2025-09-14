<?php
// ============================================
// app/Services/AchievementService.php
// ============================================
namespace App\Services;

use App\Models\User;
use App\Models\Achievement;

class AchievementService
{
    public function checkTaskAchievements(User $user): void
    {
        $completedTasks = $user->tasks()->where('status', 'completed')->count();

        // Verificar conquistas baseadas em número de tarefas
        $this->checkTaskCountAchievements($user, $completedTasks);

        // Verificar conquistas baseadas em nível
        $this->checkLevelAchievements($user);
    }

    private function checkTaskCountAchievements(User $user, int $completedTasks): void
    {
        $taskMilestones = [
            1 => 'first_task',
            5 => 'task_warrior',
            10 => 'task_master',
            25 => 'task_legend',
            50 => 'task_god'
        ];

        foreach ($taskMilestones as $count => $condition) {
            if ($completedTasks >= $count) {
                $this->awardAchievement($user, $condition);
            }
        }
    }

    private function checkLevelAchievements(User $user): void
    {
        $levelMilestones = [
            5 => 'level_5',
            10 => 'level_10',
            20 => 'level_20',
            50 => 'level_50'
        ];

        foreach ($levelMilestones as $level => $condition) {
            if ($user->level >= $level) {
                $this->awardAchievement($user, $condition);
            }
        }
    }

    private function awardAchievement(User $user, string $condition): void
    {
        $achievement = Achievement::where('condition', $condition)->first();

        if ($achievement && !$user->achievements->contains($achievement)) {
            $user->achievements()->attach($achievement, [
                'earned_at' => now()
            ]);

            // Dar pontos bonus da conquista
            $user->addExperience($achievement->points_reward);

            session()->flash('achievement', "Nova conquista desbloqueada: {$achievement->name}!");
        }
    }

    public function getUserAchievements(User $user)
    {
        return $user->achievements()
            ->withPivot('earned_at')
            ->orderBy('user_achievements.earned_at', 'desc')
            ->get();
    }

    public function getAllAchievements()
    {
        return Achievement::orderBy('points_reward', 'asc')->get();
    }

    public function getAchievementProgress(User $user): array
    {
        $totalAchievements = Achievement::count();
        $userAchievements = $user->achievements()->count();
        $percentage = $totalAchievements > 0 ? ($userAchievements / $totalAchievements) * 100 : 0;

        return [
            'total' => $totalAchievements,
            'earned' => $userAchievements,
            'percentage' => round($percentage, 1),
            'remaining' => $totalAchievements - $userAchievements
        ];
    }
}
