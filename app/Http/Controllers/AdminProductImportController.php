<?php

namespace App\Http\Controllers;

use App\Services\ProductImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProductImportController extends Controller
{
    public function __construct(private ProductImportService $import)
    {
    }

    public function form()
    {
        $categories = DB::table('categories')->orderBy('name')->get();

        return view('admin.products-import', compact('categories'));
    }

    public function template()
    {
        return response($this->import->templateCsv(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products-import-template.csv"',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'],
            'images_zip' => ['nullable', 'file', 'mimes:zip', 'max:512000'],
        ], [
            'csv_file.required' => 'Завантаж CSV файл',
            'csv_file.mimes' => 'CSV має бути у форматі .csv',
            'images_zip.mimes' => 'Архів має бути ZIP',
        ]);

        $imagesPath = null;
        if ($request->hasFile('images_zip')) {
            $imagesPath = $request->file('images_zip')->getRealPath();
        }

        $result = $this->import->importFromCsv(
            $validated['csv_file']->getRealPath(),
            $imagesPath
        );

        $message = "Імпортовано {$result['created']} товар(ів).";

        if ($result['skipped'] > 0) {
            $message .= " Пропущено: {$result['skipped']}.";
        }

        return redirect('/admin/products/import')->with([
            'status' => $message,
            'importErrors' => $result['errors'],
        ]);
    }
}
