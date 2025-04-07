<?php

$directory = __DIR__.'/src'; // Change to your generated lib folder

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);

foreach ($files as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $contents = file_get_contents($file->getPathname());

    // Regex to find nullable params without a default value
    $updated = preg_replace_callback(
        '/function\s+\w+\s*\(([^)]*)\)/m',
        function ($matches) {
            $params = $matches[1];

            $fixed = preg_replace_callback(
                '/(?<!= )(\?\s*\w+)\s+\$(\w+)/',
                function ($paramMatches) {
                    return "{$paramMatches[1]} \${$paramMatches[2]} = null";
                },
                $params
            );

            return str_replace($params, $fixed, $matches[0]);
        },
        $contents
    );

    if ($updated !== $contents) {
        file_put_contents($file->getPathname(), $updated);
        echo 'Fixed: '.$file->getPathname()."\n";
    }
}
