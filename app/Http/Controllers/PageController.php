<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    public function show($slug)
    {
        if (! Schema::hasTable('cms_pages')) {
            abort(404);
        }

        $page = DB::table('cms_pages')
            ->where('slug', $slug)
            ->where('is_published', 1)
            ->first();

        if (! $page) {
            abort(404);
        }

        if ($slug === 'about') {
            $about = $this->parseAboutContent($page->content ?? '');

            return view('pages.about', compact('page', 'about'));
        }

        return view('pages.cms', compact('page'));
    }

    private function parseAboutContent(string $content): array
    {
        $parts = preg_split('/\r\n|\n|\r---VALUES---\r\n|\n---VALUES---\n|\r\n---VALUES---\n|\n---VALUES---\r\n/', $content, 2);
        $storyRaw = trim($parts[0] ?? '');
        $valuesRaw = trim($parts[1] ?? '');

        $story = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $storyRaw))));

        $values = [
            'title' => 'Мінімалізм, зручність, стиль',
            'text' => 'Ми робимо акцент на якості сервісу, швидкій доставці та речах, які легко поєднувати між собою.',
            'tags' => ['Сучасний дизайн', 'Зручна структура', 'Доставка по Україні', 'Орієнтація на клієнта'],
        ];

        if ($valuesRaw !== '') {
            $valueLines = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $valuesRaw))));

            if (isset($valueLines[0])) {
                $values['title'] = $valueLines[0];
            }
            if (isset($valueLines[1])) {
                $values['text'] = $valueLines[1];
            }
            if (isset($valueLines[2])) {
                $values['tags'] = array_values(array_filter(array_map('trim', explode(',', $valueLines[2]))));
            }
        }

        return [
            'story' => $story,
            'story_heading' => 'Магазин одягу, який виглядає сучасно і працює зручно',
            'values' => $values,
        ];
    }
}
