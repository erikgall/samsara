<?php

/*
 * This script was used to fix the nullable method attributes that did
 * not have their default values set to null. This was a pain because
 * it required you to explicitly pass method arguments as null.
 */

$directory = __DIR__.'/src'; // Set this to your actual path

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);

foreach ($files as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $contents = file_get_contents($file->getPathname());

    // Match function name + parameters + optional return type
    $updated = preg_replace_callback(
        '/function\s+(\w+)\s*\(([^)]*)\)(\s*:\s*[^{\s]+)?/m',
        function ($matches) {
            $fnName = $matches[1];
            $params = $matches[2];
            $returnType = $matches[3] ?? '';

            // Split by comma (ignoring nested brackets or generics)
            $parts = preg_split('/,(?![^\(]*\))/', $params);

            $fixedParts = array_map(function ($param) {
                $param = trim($param);

                // Skip promoted constructor properties
                if (preg_match('/\b(public|protected|private)\b/', $param)) {
                    return $param;
                }

                // Skip already-defaulted values
                if (str_contains($param, '=')) {
                    return $param;
                }

                // Match nullable types like "?int $x" or "?Foo\Bar $baz"
                if (preg_match('/^\?\s*[\w\\\\]+\s+\$\w+$/', $param)) {
                    return $param.' = null';
                }

                return $param;
            }, $parts);

            $fixedParamString = implode(', ', $fixedParts);

            return "function {$fnName}({$fixedParamString}){$returnType}";
        },
        $contents
    );

    if ($updated !== $contents) {
        file_put_contents($file->getPathname(), $updated);
        echo 'Fixed: '.$file->getPathname()."\n";
    }
}
