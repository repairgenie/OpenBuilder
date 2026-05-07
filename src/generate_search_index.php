<?php
// src/generate_search_index.php

function generate_index() {
    $docs_dir = __DIR__ . '/../docs';
    $index = [];

    $languages = ['en', 'es'];

    foreach ($languages as $lang) {
        $lang_dir = "$docs_dir/$lang";
        if (!is_dir($lang_dir)) continue;

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($lang_dir));
        foreach ($files as $file) {
            if ($file->isDir() || $file->getExtension() !== 'md') continue;

            $relative_path = substr($file->getPathname(), strlen($lang_dir) + 1, -3); // remove .md
            $content = file_get_contents($file->getPathname());
            
            // Extract title (first H1)
            preg_match('/^# (.*)/', $content, $matches);
            $title = $matches[1] ?? $relative_path;

            $index[] = [
                'title' => $title,
                'path' => $relative_path,
                'lang' => $lang,
                'url' => "?page=docs&doc={$relative_path}&lang={$lang}"
            ];
        }
    }

    file_put_contents(__DIR__ . '/../public/search.json', json_encode($index, JSON_PRETTY_PRINT));
    return count($index);
}

if (php_sapi_name() === 'cli') {
    echo "Generated index with " . generate_index() . " items.\n";
}
