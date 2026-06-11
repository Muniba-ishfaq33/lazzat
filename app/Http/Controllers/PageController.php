<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function recipes(): View
    {
        return view('recipes');
    }

    public function recipeDetail(): View
    {
        return view('recipe-detail');
    }

    public function planner(): View
    {
        return view('planner');
    }

    public function grocery(): View
    {
        return view('grocery');
    }

    public function login(): View
    {
        return view('login');
    }

    public function register(): View
    {
        return view('register');
    }

    public function dashboard(): View
    {
        return view('dashboard');
    }
}
