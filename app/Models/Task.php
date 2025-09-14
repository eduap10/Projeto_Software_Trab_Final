<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'priority', 'status', 'due_date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Marcar como concluída
    public function complete(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    // Pontuação baseada na prioridade
    public function calculatePoints(): int
    {
        return match ($this->priority) {
            'high'   => 30,
            'medium' => 20,
            'low'    => 10,
            default  => 15,
        };
    }

    // Verificar atraso
    public function isOverdue(): bool
    {
        return $this->due_date && Carbon::parse($this->due_date)->isPast() && $this->status !== 'completed';
    }
}
