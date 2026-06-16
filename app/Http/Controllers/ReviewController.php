<?php

namespace App\Http\Controllers;

use App\Services\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $row = [
            'product_id' => (int) $productId,
            'user_id' => Auth::id(),
            'author_name' => Auth::user()->name ?? 'Користувач',
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'],
            'created_at' => now(),
        ];

        if (Schema::hasColumn('reviews', 'is_approved')) {
            $row['is_approved'] = SiteSettings::reviewsModerationEnabled() ? 0 : 1;
        }

        DB::table('reviews')->insert($row);

        $message = SiteSettings::reviewsModerationEnabled()
            ? 'Дякуємо! Відгук зʼявиться на сайті після модерації.'
            : 'Дякуємо за відгук!';

        return back()->with('reviewSuccess', $message);
    }
}
