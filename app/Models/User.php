<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'experience_points'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relacionamentos
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    // Métodos de gamificação
    public function addExperience(int $points): void
    {
        $this->experience_points += $points;
        $this->checkLevelUp();
        $this->save();
    }

    private function checkLevelUp(): void
    {
        $requiredExp = $this->level * 100; // 100 pontos por nível
        if ($this->experience_points >= $requiredExp) {
            $this->level++;
        }
    }
}
