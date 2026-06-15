<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
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

        foreach ($folders as $folderName => $productIds) {
            $items = [];

            if (! empty($productIds)) {
                $rows = DB::table('products')->whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($productIds as $productId) {
                    if (isset($rows[$productId])) {
                        $items[] = (array) $rows[$productId];
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
        ];

        return view('favorites', compact('data'));
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
