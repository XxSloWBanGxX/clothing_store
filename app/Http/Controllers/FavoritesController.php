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

    public function index()
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

        $data = ['foldersData' => $foldersData];

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
        }

        session(['favorite_folders' => $folders]);

        return back()->with('success', 'Додано в обране!');
    }

    public function createFolder(Request $request)
    {
        $folderName = trim((string) $request->input('folder_name', ''));
        $folders = $this->folders();

        if ($folderName !== '' && ! isset($folders[$folderName])) {
            $folders[$folderName] = [];
        }

        session(['favorite_folders' => $folders]);

        return redirect('/favorites');
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

        return redirect('/favorites');
    }

    public function clearFolder(Request $request)
    {
        $folder = trim((string) $request->input('folder', ''));
        $folders = $this->folders();

        if ($folder !== '' && isset($folders[$folder])) {
            $folders[$folder] = [];
        }

        session(['favorite_folders' => $folders]);

        return redirect('/favorites');
    }

    public function deleteFolder(Request $request)
    {
        $folder = trim((string) $request->input('folder', ''));
        $folders = $this->folders();

        if ($folder !== '' && $folder !== 'Обране' && isset($folders[$folder])) {
            unset($folders[$folder]);
        }

        session(['favorite_folders' => $folders]);

        return redirect('/favorites');
    }
}
