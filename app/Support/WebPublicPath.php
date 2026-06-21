<?php

namespace App\Support;

class WebPublicPath
{
    /**
     * Public web root: on ADM "www/" is document root, not Laravel's public/.
     */
    public static function resolve(string $relative = ''): string
    {
        $relative = ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relative), DIRECTORY_SEPARATOR);

        $wwwRoot = dirname(base_path()) . DIRECTORY_SEPARATOR . 'www';
        $indexFile = $wwwRoot . DIRECTORY_SEPARATOR . 'index.php';

        if (is_file($indexFile)) {
            $index = @file_get_contents($indexFile);
            if ($index !== false && str_contains($index, 'clothstore-upload')) {
                return $relative !== ''
                    ? $wwwRoot . DIRECTORY_SEPARATOR . $relative
                    : $wwwRoot;
            }
        }

        return $relative !== '' ? public_path($relative) : public_path();
    }
}
