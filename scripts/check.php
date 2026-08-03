<?php

$directories = ['src', 'config', 'routes', 'database'];

foreach ($directories as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../'.$directory));

    foreach ($iterator as $file) {
        if (! $file->isFile() || ! in_array($file->getExtension(), ['php', 'stub'], true)) {
            continue;
        }

        passthru(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file->getPathname()), $status);

        if ($status !== 0) {
            exit($status);
        }
    }
}
