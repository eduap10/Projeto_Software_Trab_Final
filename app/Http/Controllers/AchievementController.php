<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userAchievements = $user->achievements()->get();
        $allAchievements = Achievement::all();

        return view('achievements.index', compact('userAchievements', 'allAchievements'));
    }

    public function show(Achievement $achievement)
    {
        return view('achievements.show', ['achievement' => $achievement]);
    }
}
