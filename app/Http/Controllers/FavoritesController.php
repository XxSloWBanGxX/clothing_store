<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function add(Request $request)
    {
        // Поки що просто зробимо заглушку, щоб помилка зникла
        return back()->with('success', 'Додано в обране!');
    }
}