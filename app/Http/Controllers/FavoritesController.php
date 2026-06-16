<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FavoritesController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    private function folders(): array
    {
        return session('favorite_folders', ['Обране' => []]);
    }

    private function redirectToFavorites(?string $folder = null)
    {
        $url = url('/favorites');

        if ($folder !== null && $folder !== '') {
            $url .= '?folder=' . urlencode($folder);
        }

        return redirect($url);
    }

    public function index(Request $request)
    {
        $folders = $this->folders();
        $foldersData = [];
        $priceAlerts = [];

        if (Schema::hasTable('favorite_price_alerts') && Auth::check()) {
            $priceAlerts = DB::table('favorite_price_alerts')
                ->where('user_id', Auth::id())
                ->whereNull('notified_at')
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        foreach ($folders as $folderName => $productIds) {
            $items = [];

            if (! empty($productIds)) {
                $rows = DB::table('products')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->select('products.*', 'categories.name as category_name')
                    ->whereIn('products.id', $productIds)
                    ->get()
                    ->keyBy('id');

                foreach ($productIds as $productId) {
                    if (isset($rows[$productId])) {
                        $items[] = $this->pricing->applyToProduct((array) $rows[$productId]);
                    }
                }
            }

            $foldersData[$folderName] = $items;
        }

        $activeFolder = trim((string) $request->query('folder', ''));
        if ($activeFolder === '' || ! array_key_exists($activeFolder, $foldersData)) {
            $activeFolder = array_key_first($foldersData) ?: 'Обране';
        }

        $data = [
            'foldersData' => $foldersData,
            'activeFolder' => $activeFolder,
            'activeItems' => $foldersData[$activeFolder] ?? [],
            'priceAlerts' => $priceAlerts,
        ];

        return view('favorites', compact('data'));
    }

    public function share(Request $request)
    {
        $folder = trim((string) $request->input('folder', 'Обране'));
        $folders = $this->folders();

        if (! isset($folders[$folder]) || empty($folders[$folder])) {
            return back()->withErrors(['share' => 'Список порожній — додай товари перед поширенням']);
        }

        if (! Schema::hasTable('favorite_shares')) {
            return back()->withErrors(['share' => 'Поширення тимчасово недоступне']);
        }

        $token = Str::random(48);

        DB::table('favorite_shares')->insert([
            'token' => $token,
            'folder_name' => $folder,
            'product_ids' => json_encode(array_values($folders[$folder])),
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        $url = url('/favorites/share/' . $token);

        return back()->with('shareUrl', $url)->with('success', 'Посилання на список «' . $folder . '» створено');
    }

    public function showShare(string $token)
    {
        if (! Schema::hasTable('favorite_shares')) {
            abort(404);
        }

        $share = DB::table('favorite_shares')->where('token', $token)->first();
        if (! $share) {
            abort(404);
        }

        $productIds = json_decode($share->product_ids, true) ?: [];
        $items = [];

        if (! empty($productIds)) {
            $rows = DB::table('products')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->select('products.*', 'categories.name as category_name')
                ->whereIn('products.id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($productIds as $productId) {
                if (isset($rows[$productId])) {
                    $items[] = $this->pricing->applyToProduct((array) $rows[$productId]);
                }
            }
        }

        return view('favorites-share', [
            'share' => $share,
            'items' => $items,
        ]);
    }

    public function importShare(string $token)
    {
        if (! Schema::hasTable('favorite_shares')) {
            return redirect('/favorites');
        }

        $share = DB::table('favorite_shares')->where('token', $token)->first();
        if (! $share) {
            abort(404);
        }

        $folders = $this->folders();
        $folderName = $share->folder_name ?: 'Обране';
        if (! isset($folders[$folderName])) {
            $folders[$folderName] = [];
        }

        foreach (json_decode($share->product_ids, true) ?: [] as $productId) {
            $productId = (int) $productId;
            if ($productId > 0 && ! in_array($productId, $folders[$folderName], true)) {
                $folders[$folderName][] = $productId;
            }
        }

        session(['favorite_folders' => $folders]);

        return redirect('/favorites?folder=' . urlencode($folderName))
            ->with('success', 'Список «' . $folderName . '» додано в твоє обране');
    }

    public function priceAlert(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'email' => ['required', 'email', 'max:150'],
        ]);

        if (! Schema::hasTable('favorite_price_alerts')) {
            return back()->with('success', 'Сповіщення увімкнено');
        }

        $product = DB::table('products')->where('id', $validated['product_id'])->first();
        $price = $this->pricing->getEffectivePrice($product);

        DB::table('favorite_price_alerts')->updateOrInsert(
            ['product_id' => (int) $validated['product_id'], 'email' => $validated['email']],
            [
                'user_id' => Auth::id(),
                'watched_price' => $price,
                'notified_at' => null,
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Повідомимо про знижку на ' . $validated['email']);
    }

    public function add(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $folder = trim((string) $request->input('folder', 'Обране'));

        $folders = $this->folders();

        if (! isset($folders[$folder])) {
            $folder = 'Обране';
            if (! isset($folders[$folder])) {
                $folders[$folder] = [];
            }
        }

        if ($productId > 0 && ! in_array($productId, $folders[$folder], true)) {
            $folders[$folder][] = $productId;
            session(['favorite_folders' => $folders]);

            return back()->with('success', 'Додано в список «' . $folder . '»!');
        }

        session(['favorite_folders' => $folders]);

        return back()->with('success', 'Товар уже є в списку «' . $folder . '».');
    }

    public function move(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $fromFolder = trim((string) $request->input('from_folder', ''));
        $toFolder = trim((string) $request->input('to_folder', ''));

        $folders = $this->folders();

        if ($productId <= 0 || $fromFolder === '' || $toFolder === '' || $fromFolder === $toFolder) {
            return $this->redirectToFavorites($fromFolder !== '' ? $fromFolder : null);
        }

        if (! isset($folders[$fromFolder]) || ! isset($folders[$toFolder])) {
            return $this->redirectToFavorites($fromFolder);
        }

        $folders[$fromFolder] = array_values(array_filter(
            $folders[$fromFolder],
            fn ($id) => (int) $id !== $productId
        ));

        if (! in_array($productId, $folders[$toFolder], true)) {
            $folders[$toFolder][] = $productId;
        }

        session(['favorite_folders' => $folders]);

        return $this->redirectToFavorites($fromFolder)->with(
            'success',
            'Товар переміщено в «' . $toFolder . '».'
        );
    }

    public function createFolder(Request $request)
    {
        $folderName = trim((string) $request->input('folder_name', ''));
        $folders = $this->folders();

        if ($folderName !== '' && ! isset($folders[$folderName])) {
            $folders[$folderName] = [];
        }

        session(['favorite_folders' => $folders]);

        return $this->redirectToFavorites($folderName !== '' ? $folderName : null);
    }

    public function remove(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $folder = trim((string) $request->input('folder', ''));
        $folders = $this->folders();

        if ($folder !== '' && isset($folders[$folder])) {
            $folders[$folder] = array_values(array_filter(
                $folders[$folder],
                fn ($id) => (int) $id !== $productId
            ));
        }

        session(['favorite_folders' => $folders]);

        return $this->redirectToFavorites($folder !== '' ? $folder : null);
    }

    public function clearFolder(Request $request)
    {
        $folder = trim((string) $request->input('folder', ''));
        $folders = $this->folders();

        if ($folder !== '' && isset($folders[$folder])) {
            $folders[$folder] = [];
        }

        session(['favorite_folders' => $folders]);

        return $this->redirectToFavorites($folder !== '' ? $folder : null);
    }

    public function deleteFolder(Request $request)
    {
        $folder = trim((string) $request->input('folder', ''));
        $folders = $this->folders();

        if ($folder !== '' && $folder !== 'Обране' && isset($folders[$folder])) {
            unset($folders[$folder]);
        }

        session(['favorite_folders' => $folders]);

        return $this->redirectToFavorites('Обране');
    }
}
