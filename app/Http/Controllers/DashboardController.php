<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Exibe a tela inicial (dashboard).
     */
    public function index()
    {
        return view('dashboard');
    }
}
