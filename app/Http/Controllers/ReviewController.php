<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $product = DB::table('products')->where('id', $productId)->first();

        if (! $product) {
            abort(404);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'rating.required' => 'Постав оцінку',
            'comment.required' => 'Напиши відгук',
            'comment.min' => 'Відгук занадто короткий',
        ]);

        DB::table('reviews')->insert([
            'product_id' => (int) $productId,
            'user_id' => Auth::id(),
            'author_name' => Auth::user()->name ?? 'Користувач',
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'],
            'created_at' => now(),
        ]);

        return back()->with('reviewSuccess', 'Дякуємо за відгук!');
    }
}
