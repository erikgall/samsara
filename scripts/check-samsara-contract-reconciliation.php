<?php

declare(strict_types=1);

/**
 * Dependency-free, read-only reconciliation audit for docs/api-sync/contract-inventory.json.
 *
 * The checker never writes. It derives the official operation declarations and
 * lifecycle stages from the pinned OpenAPI document, derives inherited generic
 * operations from current PHP source, and verifies the inventory ledger and
 * provenance against both authorities. It also derives the tracked package
 * enum and DTO annotation catalogs from current source so their evidence
 * cannot remain stale behind a matching source-tree digest.
 *
 * Usage:
 *   php scripts/check-samsara-contract-reconciliation.php
 *   php scripts/check-samsara-contract-reconciliation.php --root /path/to/repo
 */
const HTTP_METHODS = ['get', 'post', 'put', 'patch', 'delete', 'options', 'head', 'trace'];
const GENERIC_OPERATIONS = [
    'all'    => ['GET', false],
    'find'   => ['GET', true],
    'create' => ['POST', false],
    'update' => ['PATCH', true],
    'delete' => ['DELETE', true],
];
const CLASSIFICATIONS = ['breaking', 'additive', 'docs-only'];

$options = parseArguments(array_slice($argv, 1));
$root = realpath($options['root']) ?: $options['root'];
$inventoryPath = absolutePath($root, $options['inventory']);
$contractPath = absolutePath($root, $options['contract']);
$errors = [];
$passes = 0;

try {
    $inventory = decodeJsonFile($inventoryPath, 'Inventory');
    $contract = decodeJsonFile($contractPath, 'Contract');

    $official = deriveOfficialOperations($contract, $errors);
    $package = deriveInventoryOperations($inventory, $official, $root, $errors);
    $capabilities = deriveInheritedCapabilities($root, $official['wire_keys'], $errors);
    $source = deriveSourceIdentity($root, $errors);
    $declarations = derivePackageDeclarations($root, $errors);

    verifyRecordedSourceContext($inventory, $errors, $passes);
    checkSame('generated date', '2026-08-10', $inventory['generated_for_date'] ?? null, $errors, $passes);

    verifyOfficialSource($inventory, $contractPath, $official, $errors, $passes);
    verifySourceIdentity($inventory, $source, $errors, $passes);
    verifyCounts($inventory, $official, $package, $capabilities, $declarations, $errors, $passes);
    verifyStages($inventory, $official, $package, $errors, $passes);
    verifyCapabilities($inventory, $package, $capabilities, $errors, $passes);
    verifyCoverage($inventory, $official, $package, $capabilities, $source, $errors, $passes);
    verifyPackageDeclarations($inventory, $declarations, $errors, $passes);

    fwrite(STDOUT, "Derived official declarations: {$official['declaration_count']}\n");
    fwrite(STDOUT, 'Derived official unique wire operations: '.count($official['wire_keys'])."\n");
    fwrite(STDOUT, 'Derived package operations from ledger: '.count($package['keys'])."\n");
    fwrite(STDOUT, "Derived matched/package-only/official-only(unique): {$package['matched_count']}/{$package['package_only_count']}/{$package['official_only_unique_count']}\n");
    fwrite(STDOUT, "Derived changed operations: {$package['changed_count']}\n");
    fwrite(STDOUT, 'Derived official stages: '.formatStageCounts($official['stage_counts'])."\n");
    fwrite(STDOUT, 'Derived official-only stages: '.formatStageCounts($package['official_only_stage_counts'])."\n");
    fwrite(STDOUT, "Derived inherited operations/phantoms: {$capabilities['effective_count']}/{$capabilities['phantom_count']}\n");
    fwrite(STDOUT, "Derived concrete resource capability overrides: {$capabilities['override_count']}/{$capabilities['resource_count']}\n");
    fwrite(STDOUT, "Derived src tree: {$source['file_count']} files, SHA-256 {$source['tree_sha256']}\n");
    fwrite(STDOUT, "Derived package enums/cases: {$declarations['enum_count']}/{$declarations['enum_case_count']}\n");
    fwrite(STDOUT, "Derived package data classes/properties: {$declarations['data_class_count']}/{$declarations['data_property_count']}\n");

    if ($errors !== []) {
        fwrite(STDERR, "\nRECONCILIATION FAILED (".count($errors)." issue(s)):\n");
        foreach ($errors as $index => $error) {
            fwrite(STDERR, sprintf("%d. %s\n", $index + 1, $error));
        }
        exit(1);
    }

    fwrite(STDOUT, "PASS: {$passes} reconciliation assertions; unclassified differences = 0.\n");
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, "Checker failed: {$exception->getMessage()}\n");
    exit(2);
}

/** @return array{root:string, inventory:string, contract:string} */
function parseArguments(array $arguments): array
{
    $options = [
        'root'      => getcwd(),
        'inventory' => 'docs/api-sync/contract-inventory.json',
        'contract'  => 'contracts/samsara/samsara-api-2026-08-10.json',
    ];

    for ($i = 0; $i < count($arguments); $i += 2) {
        $flag = $arguments[$i] ?? '';
        $value = $arguments[$i + 1] ?? null;
        if (! in_array($flag, ['--root', '--inventory', '--contract'], true) || $value === null) {
            throw new RuntimeException('Expected --root, --inventory, or --contract followed by a value.');
        }
        $options[substr($flag, 2)] = $value;
    }

    return $options;
}

function absolutePath(string $root, string $path): string
{
    return str_starts_with($path, '/') ? $path : $root.'/'.$path;
}

/** @return array<string,mixed> */
function decodeJsonFile(string $path, string $label): array
{
    $bytes = file_get_contents($path);
    if ($bytes === false || $bytes === '') {
        throw new RuntimeException("{$label} is missing, empty, or unreadable: {$path}");
    }
    $decoded = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
    if (! is_array($decoded)) {
        throw new RuntimeException("{$label} root must be a JSON object: {$path}");
    }

    return $decoded;
}

function normalizePath(string $path): string
{
    return preg_replace('/\{[^}]+\}/', '{}', $path) ?? $path;
}

function operationKey(string $method, string $path): string
{
    return strtoupper($method).' '.normalizePath($path);
}

function operationStage(array $tags): string
{
    $beta = in_array('Beta APIs', $tags, true);
    $preview = in_array('Preview APIs', $tags, true);
    if ($beta && $preview) {
        throw new RuntimeException('An official operation is tagged as both Beta and Preview.');
    }

    return $preview ? 'preview' : ($beta ? 'beta' : 'stable');
}

/**
 * @return array{
 *   declaration_count:int,
 *   wire_keys:array<string,true>,
 *   declarations_by_key:array<string,list<array{operation_id:string,method:string,path:string,stage:string}>>,
 *   operation_ids:array<string,true>,
 *   stage_counts:array{stable:int,beta:int,preview:int},
 *   component_schema_count:int,
 *   sha256:string,
 *   bytes:int
 * }
 */
function deriveOfficialOperations(array $contract, array &$errors): array
{
    $paths = $contract['paths'] ?? null;
    if (! is_array($paths)) {
        throw new RuntimeException('Contract paths must be an object.');
    }

    $byKey = [];
    $ids = [];
    $stages = ['stable' => 0, 'beta' => 0, 'preview' => 0];
    $count = 0;
    foreach ($paths as $path => $pathItem) {
        if (! is_array($pathItem)) {
            continue;
        }
        foreach (HTTP_METHODS as $method) {
            if (! isset($pathItem[$method]) || ! is_array($pathItem[$method])) {
                continue;
            }
            $operation = $pathItem[$method];
            $id = $operation['operationId'] ?? null;
            if (! is_string($id) || $id === '') {
                $errors[] = "Official {$method} {$path} has no operationId.";
                $id = "<missing:{$method}:{$path}>";
            }
            if (isset($ids[$id])) {
                $errors[] = "Duplicate official operationId: {$id}.";
            }
            $ids[$id] = true;
            $stage = operationStage(is_array($operation['tags'] ?? null) ? $operation['tags'] : []);
            $key = operationKey($method, (string) $path);
            $byKey[$key][] = [
                'operation_id' => $id,
                'method'       => strtoupper($method),
                'path'         => (string) $path,
                'stage'        => $stage,
            ];
            $stages[$stage]++;
            $count++;
        }
    }

    $schemas = $contract['components']['schemas'] ?? [];

    return [
        'declaration_count'      => $count,
        'wire_keys'              => array_fill_keys(array_keys($byKey), true),
        'declarations_by_key'    => $byKey,
        'operation_ids'          => $ids,
        'stage_counts'           => $stages,
        'component_schema_count' => is_array($schemas) ? count($schemas) : 0,
        'sha256'                 => '',
        'bytes'                  => 0,
    ];
}

/**
 * @return array{
 *   keys:array<string,true>, matched_count:int, official_only_unique_count:int,
 *   official_only_declaration_count:int, package_only_count:int, changed_count:int,
 *   accurate_count:int, official_only_stage_counts:array{stable:int,beta:int,preview:int},
 *   official_only_stable_ids:list<string>, actual_unclassified:int,
 *   method_keys:array<string,true>
 * }
 */
function deriveInventoryOperations(array $inventory, array $official, string $root, array &$errors): array
{
    $operations = $inventory['operations'] ?? null;
    if (! is_array($operations)) {
        throw new RuntimeException('Inventory operations must be an array.');
    }

    $entryByKey = [];
    $packageKeys = [];
    $actualOfficial = [];
    $changed = 0;
    $accurate = 0;
    $unclassified = 0;

    foreach ($operations as $index => $entry) {
        if (! is_array($entry)) {
            $errors[] = "operations[{$index}] is not an object.";

            continue;
        }
        $key = $entry['key'] ?? null;
        if (! is_string($key) || $key === '') {
            $errors[] = "operations[{$index}] has no key.";

            continue;
        }
        if (isset($entryByKey[$key])) {
            $errors[] = "Duplicate inventory operation key: {$key}.";
        }
        $entryByKey[$key] = $entry;

        if (is_array($entry['official'] ?? null)) {
            $officialKey = inventorySideKey($entry['official']);
            if ($officialKey !== $key) {
                $errors[] = "{$key}: official side normalizes to {$officialKey}.";
            }
            $actualOfficial[$officialKey] = true;
            verifyOfficialEntry($key, $entry['official'], $official['declarations_by_key'][$key] ?? [], $errors);
        }
        if (is_array($entry['package'] ?? null)) {
            $packageKey = inventorySideKey($entry['package']);
            if ($packageKey !== $key) {
                $errors[] = "{$key}: package side normalizes to {$packageKey}.";
            }
            $packageKeys[$packageKey] = true;
            verifyPackageEvidence($entry['package'], $root, $errors);
        }

        $differences = is_array($entry['differences'] ?? null) ? $entry['differences'] : [];
        foreach ($differences as $differenceIndex => $difference) {
            if (! is_array($difference) || ! in_array($difference['classification'] ?? null, CLASSIFICATIONS, true)) {
                $errors[] = "{$key}: difference {$differenceIndex} lacks a supported classification.";
                $unclassified++;
            }
        }
        if (($entry['unclassified'] ?? false) !== false) {
            $unclassified++;
        }
        if (($entry['changed'] ?? false) === true) {
            $changed++;
        }
    }

    foreach (($inventory['cross_cutting_differences'] ?? []) as $index => $difference) {
        if (! is_array($difference) || ! in_array($difference['classification'] ?? null, CLASSIFICATIONS, true)) {
            $errors[] = "cross_cutting_differences[{$index}] lacks a supported classification.";
            $unclassified++;
        }
        if (($difference['status'] ?? null) === 'unclassified') {
            $unclassified++;
        }
    }

    $methodKeys = [];
    foreach (($inventory['package_endpoint_methods'] ?? []) as $index => $method) {
        if (! is_array($method)) {
            $errors[] = "package_endpoint_methods[{$index}] is not an object.";

            continue;
        }
        foreach (($method['operations'] ?? []) as $operationIndex => $operation) {
            if (! is_array($operation) || ! is_string($operation[0] ?? null) || ! is_string($operation[1] ?? null)) {
                $errors[] = "package_endpoint_methods[{$index}].operations[{$operationIndex}] is not [method,path].";

                continue;
            }
            $methodKeys[operationKey($operation[0], $operation[1])] = true;
        }
    }

    compareSets('official operation keys represented in ledger', $official['wire_keys'], $actualOfficial, $errors);
    compareSets('package operation keys represented by endpoint methods', $packageKeys, $methodKeys, $errors);

    $matchedKeys = array_intersect_key($official['wire_keys'], $packageKeys);
    $officialOnlyKeys = array_diff_key($official['wire_keys'], $packageKeys);
    $packageOnlyKeys = array_diff_key($packageKeys, $official['wire_keys']);
    $stageCounts = ['stable' => 0, 'beta' => 0, 'preview' => 0];
    $stableIds = [];
    $officialOnlyDeclarations = 0;

    foreach ($officialOnlyKeys as $key => $_) {
        foreach ($official['declarations_by_key'][$key] as $declaration) {
            $officialOnlyDeclarations++;
            $stageCounts[$declaration['stage']]++;
            if ($declaration['stage'] === 'stable') {
                $stableIds[] = $declaration['operation_id'];
            }
        }
    }
    sort($stableIds, SORT_STRING);

    foreach ($entryByKey as $key => $entry) {
        $expectedPresence = isset($matchedKeys[$key]) ? 'matched'
            : (isset($officialOnlyKeys[$key]) ? 'official_only' : 'package_only');
        if (($entry['presence'] ?? null) !== $expectedPresence) {
            $errors[] = "{$key}: presence is ".json_encode($entry['presence'] ?? null).", expected {$expectedPresence}.";
        }
        if ($expectedPresence === 'matched') {
            $isChanged = ($entry['changed'] ?? false) === true;
            $hasDifferences = is_array($entry['differences'] ?? null) && $entry['differences'] !== [];
            if ($isChanged !== $hasDifferences) {
                $errors[] = "{$key}: changed must agree with whether matched differences exist.";
            }
            if (! $isChanged) {
                $accurate++;
            }
        }
    }

    return [
        'keys'                            => $packageKeys,
        'matched_count'                   => count($matchedKeys),
        'official_only_unique_count'      => count($officialOnlyKeys),
        'official_only_declaration_count' => $officialOnlyDeclarations,
        'package_only_count'              => count($packageOnlyKeys),
        'changed_count'                   => $changed,
        'accurate_count'                  => $accurate,
        'official_only_stage_counts'      => $stageCounts,
        'official_only_stable_ids'        => $stableIds,
        'actual_unclassified'             => $unclassified,
        'method_keys'                     => $methodKeys,
    ];
}

function inventorySideKey(array $side): string
{
    if (is_string($side['match_key'] ?? null)) {
        return $side['match_key'];
    }

    return operationKey((string) ($side['method'] ?? ''), (string) ($side['path'] ?? ''));
}

function verifyOfficialEntry(string $key, array $side, array $expectedDeclarations, array &$errors): void
{
    $actual = [];
    $declarations = is_array($side['declarations'] ?? null) ? $side['declarations'] : [$side];
    foreach ($declarations as $declaration) {
        if (is_array($declaration) && is_string($declaration['operation_id'] ?? null)) {
            $actual[] = $declaration['operation_id'];
        }
    }
    $expected = array_column($expectedDeclarations, 'operation_id');
    sort($actual, SORT_STRING);
    sort($expected, SORT_STRING);
    if ($actual !== $expected) {
        $errors[] = "{$key}: official operationIds differ; actual=".json_encode($actual).', expected='.json_encode($expected).'.';
    }
    if (($side['declaration_count'] ?? count($actual)) !== count($expected)) {
        $errors[] = "{$key}: declaration_count does not match the pinned contract.";
    }
}

function verifyPackageEvidence(array $side, string $root, array &$errors): void
{
    foreach (($side['sdk_methods'] ?? []) as $method) {
        if (! is_array($method)) {
            continue;
        }
        $source = $method['source'] ?? null;
        $name = $method['method'] ?? null;
        if (! is_string($source) || ! preg_match('/(?<path>src\/[A-Za-z0-9_.\/-]+\.php):(?<line>\d+)/', $source, $match)) {
            $errors[] = 'Package SDK method lacks parseable src file:line evidence: '.json_encode($method);

            continue;
        }
        $path = $root.'/'.$match['path'];
        $lines = is_file($path) ? file($path) : false;
        $line = (int) $match['line'];
        if ($lines === false || $line < 1 || $line > count($lines)) {
            $errors[] = "Package evidence does not resolve: {$source}.";

            continue;
        }
        if (is_string($name) && ! sourceWindowContainsMethod($lines, $line, $name)) {
            $errors[] = "Package evidence line is stale for {$name}: {$source}.";
        }
    }
}

function sourceWindowContainsMethod(array $lines, int $line, string $method): bool
{
    $start = max(0, $line - 4);
    $window = implode('', array_slice($lines, $start, 9));

    return preg_match('/function\s+'.preg_quote($method, '/').'\s*\(/', $window) === 1;
}

/**
 * @return array{resource_count:int,override_count:int,effective_count:int,phantom_count:int,keys:array<string,true>,resource_sources:list<string>}
 */
function deriveInheritedCapabilities(string $root, array $officialKeys, array &$errors): array
{
    $directory = $root.'/src/Resources';
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), 'Resource.php') && $file->getFilename() !== 'Resource.php') {
            $files[] = $file->getPathname();
        }
    }
    sort($files, SORT_STRING);

    $keys = [];
    $phantoms = [];
    $overrides = 0;
    $sources = [];
    foreach ($files as $file) {
        $source = file_get_contents($file);
        if ($source === false) {
            $errors[] = "Unreadable resource source: {$file}.";

            continue;
        }
        $relative = substr($file, strlen($root) + 1);
        $sources[] = $relative;
        if (! preg_match('/protected\s+string\s+\$endpoint\s*=\s*([\'\"])(?<endpoint>.*?)\1\s*;/', $source, $endpointMatch)) {
            $errors[] = "Concrete resource has no literal endpoint: {$relative}.";

            continue;
        }
        $operations = parseSupportedGenericOperations($source);
        if ($operations === null) {
            $errors[] = "Concrete resource does not override supportedGenericOperations(): {$relative}.";

            continue;
        }
        $overrides++;
        foreach ($operations as $operation) {
            if (! isset(GENERIC_OPERATIONS[$operation])) {
                $errors[] = "{$relative}: unknown generic capability {$operation}.";

                continue;
            }
            if (preg_match('/public\s+function\s+'.preg_quote($operation, '/').'\s*\(/', $source) === 1) {
                continue; // Explicit override, not inherited behavior.
            }
            [$method, $item] = GENERIC_OPERATIONS[$operation];
            $path = $endpointMatch['endpoint'].($item ? '/{}' : '');
            $key = operationKey($method, $path);
            $keys[$key] = true;
            if (! isset($officialKeys[$key])) {
                $phantoms[$relative.'::'.$operation] = $key;
            }
        }
    }

    foreach ($phantoms as $method => $key) {
        $errors[] = "Phantom inherited operation {$method} => {$key}.";
    }

    return [
        'resource_count'   => count($files),
        'override_count'   => $overrides,
        'effective_count'  => count($keys),
        'phantom_count'    => count($phantoms),
        'keys'             => $keys,
        'resource_sources' => $sources,
    ];
}

/** @return list<string>|null */
function parseSupportedGenericOperations(string $source): ?array
{
    if (! preg_match('/function\s+supportedGenericOperations\s*\(\s*\)\s*:\s*array\s*\{(?<body>.*?)\n\s*\}/s', $source, $method)) {
        return null;
    }
    if (! preg_match('/return\s*\[(?<values>.*?)\]\s*;/s', $method['body'], $return)) {
        return null;
    }
    preg_match_all('/[\'\"](?<value>[a-z]+)[\'\"]/', $return['values'], $values);

    return $values['value'];
}

/** @return array{file_count:int,php_file_count:int,tree_sha256:string} */
function deriveSourceIdentity(string $root, array &$errors): array
{
    $files = [];
    $php = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/src', FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['php', 'json'], true)) {
            $relative = substr($file->getPathname(), strlen($root) + 1);
            $files[] = $relative;
            if (str_ends_with($relative, '.php')) {
                $php++;
            }
        }
    }
    if (is_file($root.'/config/samsara.php')) {
        $files[] = 'config/samsara.php';
        $php++;
    } else {
        $errors[] = 'Missing package behavior configuration: config/samsara.php.';
    }
    sort($files, SORT_STRING);
    $manifest = '';
    foreach ($files as $relative) {
        $hash = hash_file('sha256', $root.'/'.$relative);
        if ($hash === false) {
            $errors[] = "Could not hash {$relative}.";

            continue;
        }
        $manifest .= $hash.'  '.$relative."\n";
    }

    return [
        'file_count'     => count($files),
        'php_file_count' => $php,
        'tree_sha256'    => hash('sha256', $manifest),
    ];
}

/**
 * @return array{
 *   enums:list<array{class:string,source:string,cases:list<array{case:string,value:string,source:string}>}>,
 *   data_classes:list<array{class:string,source:string,properties:list<array{name:string,php_type:string}>}>,
 *   enum_count:int,enum_case_count:int,data_class_count:int,data_property_count:int
 * }
 */
function derivePackageDeclarations(string $root, array &$errors): array
{
    $enums = [];
    foreach (phpFilesUnder($root.'/src/Enums') as $path) {
        $relative = substr($path, strlen($root) + 1);
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            $errors[] = "Unreadable enum source: {$relative}.";

            continue;
        }

        $class = null;
        $cases = [];
        foreach ($lines as $index => $line) {
            if ($class === null && preg_match('/^\s*enum\s+(?<class>[A-Za-z_][A-Za-z0-9_]*)\b/', $line, $match)) {
                $class = $match['class'];
            }
            if (preg_match('/^\s*case\s+(?<case>[A-Za-z_][A-Za-z0-9_]*)\s*=\s*([\'"])(?<value>.*?)\2\s*;\s*$/', $line, $match)) {
                $cases[] = [
                    'case'   => $match['case'],
                    'value'  => $match['value'],
                    'source' => $relative.':'.($index + 1),
                ];
            }
        }
        if ($class === null) {
            $errors[] = "Enum source has no declaration: {$relative}.";

            continue;
        }
        $enums[] = ['class' => $class, 'source' => $relative, 'cases' => $cases];
    }

    $dataClasses = [];
    foreach (phpFilesUnder($root.'/src/Data') as $path) {
        $relative = substr($path, strlen($root) + 1);
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            $errors[] = "Unreadable data-class source: {$relative}.";

            continue;
        }

        $class = null;
        $properties = [];
        foreach ($lines as $line) {
            if ($class === null && preg_match('/^\s*(?:(?:abstract|final)\s+)?class\s+(?<class>[A-Za-z_][A-Za-z0-9_]*)\b/', $line, $match)) {
                $class = $match['class'];
            }
            if (preg_match('/@property-read\s+(?<type>.+?)\s+\$(?<name>[A-Za-z_][A-Za-z0-9_]*)(?:\s|$)/', $line, $match)) {
                $properties[] = ['name' => $match['name'], 'php_type' => $match['type']];
            }
        }
        if ($class === null) {
            $errors[] = "Data-class source has no declaration: {$relative}.";

            continue;
        }
        $dataClasses[] = ['class' => $class, 'source' => $relative, 'properties' => $properties];
    }

    return [
        'enums'               => $enums,
        'data_classes'        => $dataClasses,
        'enum_count'          => count($enums),
        'enum_case_count'     => array_sum(array_map(static fn (array $enum): int => count($enum['cases']), $enums)),
        'data_class_count'    => count($dataClasses),
        'data_property_count' => array_sum(array_map(static fn (array $class): int => count($class['properties']), $dataClasses)),
    ];
}

/** @return list<string> */
function phpFilesUnder(string $directory): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
            $files[] = $file->getPathname();
        }
    }
    sort($files, SORT_STRING);

    return $files;
}

function verifyPackageDeclarations(array $inventory, array $declarations, array &$errors, int &$passes): void
{
    checkSame('package enum catalog', $declarations['enums'], $inventory['package_enums'] ?? null, $errors, $passes);
    checkSame('package data-class catalog', $declarations['data_classes'], $inventory['package_data_classes'] ?? null, $errors, $passes);
}

function verifyRecordedSourceContext(array $inventory, array &$errors, int &$passes): void
{
    $worktree = $inventory['worktree'] ?? null;
    if (! is_string($worktree) || $worktree === '') {
        $errors[] = 'Inventory worktree provenance must be a non-empty string.';
    } else {
        $passes++;
    }

    $identity = firstValue($inventory, [
        ['sources', 'current_package_source'],
        ['sources', 'package_source'],
    ]);
    $recordedRoot = is_array($identity) ? ($identity['root'] ?? $identity['worktree'] ?? null) : null;
    $recordedBranch = is_array($identity) ? ($identity['branch'] ?? $identity['git_branch'] ?? null) : null;

    if (! is_string($recordedRoot) || $recordedRoot === '') {
        $errors[] = 'Package source root provenance must be a non-empty string.';
    } else {
        $passes++;
    }

    if (! is_string($recordedBranch) || $recordedBranch === '') {
        $errors[] = 'Package source branch provenance must be a non-empty string.';
    } else {
        $passes++;
    }
}

function verifyOfficialSource(array $inventory, string $contractPath, array $official, array &$errors, int &$passes): void
{
    $source = $inventory['sources']['official_openapi'] ?? [];
    $hash = hash_file('sha256', $contractPath);
    $bytes = filesize($contractPath);
    checkSame('official source SHA-256', $hash, $source['sha256'] ?? null, $errors, $passes);
    checkSame('official source bytes', $bytes, $source['bytes'] ?? null, $errors, $passes);
    checkSame('official source operation count', $official['declaration_count'], $source['operations'] ?? null, $errors, $passes);
    checkSame('official source unique operation count', count($official['wire_keys']), $source['unique_wire_operations'] ?? null, $errors, $passes);
}

function verifySourceIdentity(array $inventory, array $source, array &$errors, int &$passes): void
{
    $identity = firstValue($inventory, [
        ['sources', 'current_package_source'],
        ['sources', 'package_source'],
    ]);
    if (! is_array($identity)) {
        $errors[] = 'Missing sources.current_package_source proof object.';

        return;
    }
    checkSame('package src tree SHA-256', $source['tree_sha256'], $identity['source_tree_sha256'] ?? $identity['source_sha256'] ?? $identity['sha256'] ?? null, $errors, $passes);
    checkSame('package src file count', $source['file_count'], $identity['source_file_count'] ?? $identity['files'] ?? null, $errors, $passes);
}

function verifyCounts(array $inventory, array $official, array $package, array $capabilities, array $declarations, array &$errors, int &$passes): void
{
    $counts = $inventory['counts'] ?? [];
    $expected = [
        'official_operations'                             => $official['declaration_count'],
        'official_unique_wire_operations'                 => count($official['wire_keys']),
        'package_operations'                              => count($package['keys']),
        'matched_operations'                              => $package['matched_count'],
        'matched_without_classified_operation_difference' => $package['accurate_count'],
        'official_only_operations'                        => $package['official_only_declaration_count'],
        'official_only_unique_wire_operations'            => $package['official_only_unique_count'],
        'package_only_operations'                         => $package['package_only_count'],
        'changed_operations'                              => $package['changed_count'],
        'unclassified_differences'                        => 0,
        'package_resources'                               => $capabilities['resource_count'],
        'package_enums'                                   => $declarations['enum_count'],
        'package_enum_cases'                              => $declarations['enum_case_count'],
        'package_data_classes'                            => $declarations['data_class_count'],
        'package_data_properties'                         => $declarations['data_property_count'],
    ];
    foreach ($expected as $field => $value) {
        checkSame("counts.{$field}", $value, $counts[$field] ?? null, $errors, $passes);
    }
    checkSame('actual unclassified differences', 0, $package['actual_unclassified'], $errors, $passes);
    checkSame(
        'official operation arithmetic',
        $counts['official_operations'] ?? null,
        ($counts['matched_operations'] ?? -1) + ($counts['official_only_operations'] ?? -1),
        $errors,
        $passes
    );
    checkSame(
        'package operation arithmetic',
        $counts['package_operations'] ?? null,
        ($counts['matched_operations'] ?? -1) + ($counts['package_only_operations'] ?? -1),
        $errors,
        $passes
    );
    checkSame(
        'matched operation arithmetic',
        $counts['matched_operations'] ?? null,
        ($counts['changed_operations'] ?? -1) + ($counts['matched_without_classified_operation_difference'] ?? -1),
        $errors,
        $passes
    );
}

function verifyStages(array $inventory, array $official, array $package, array &$errors, int &$passes): void
{
    $officialCounts = firstValue($inventory, [
        ['counts', 'official_operations_by_stage'],
        ['counts', 'official_operations_by_stability'],
        ['stage_reconciliation', 'official_counts'],
        ['lifecycle', 'official_counts'],
        ['official_contract_statistics', 'operations_by_stage'],
    ]);
    $officialOnlyCounts = firstValue($inventory, [
        ['counts', 'official_only_operations_by_stage'],
        ['counts', 'official_only_operations_by_stability'],
        ['stage_reconciliation', 'official_only_counts'],
        ['lifecycle', 'official_only_counts'],
        ['official_only_backlog', 'counts'],
    ]);
    if (! is_array($officialCounts)) {
        $errors[] = 'Missing official stable/Beta/Preview stage counts.';
    } else {
        checkSame('official stage counts', $official['stage_counts'], normalizeStageCounts($officialCounts), $errors, $passes);
    }
    if (! is_array($officialOnlyCounts)) {
        $errors[] = 'Missing official-only stable/Beta/Preview stage counts.';
    } else {
        checkSame('official-only stage counts', $package['official_only_stage_counts'], normalizeStageCounts($officialOnlyCounts), $errors, $passes);
    }

    $stableList = firstValue($inventory, [
        ['official_only_stable_operations'],
        ['stage_reconciliation', 'official_only_stable_operations'],
        ['lifecycle', 'official_only_stable_operations'],
        ['official_only_backlog', 'stable_operations'],
        ['official_only_backlog', 'stable'],
    ]);
    if (! is_array($stableList)) {
        $errors[] = 'Missing exhaustive official-only stable GA operation list.';

        return;
    }
    $actualIds = [];
    foreach ($stableList as $item) {
        if (is_string($item)) {
            $actualIds[] = $item;
        } elseif (is_array($item) && is_string($item['operation_id'] ?? null)) {
            $actualIds[] = $item['operation_id'];
        }
    }
    sort($actualIds, SORT_STRING);
    checkSame('official-only stable GA operation IDs', $package['official_only_stable_ids'], $actualIds, $errors, $passes);
}

function verifyCapabilities(array $inventory, array $package, array $capabilities, array &$errors, int &$passes): void
{
    foreach ($capabilities['keys'] as $key => $_) {
        if (! isset($package['keys'][$key])) {
            $errors[] = "Effective inherited operation missing from package ledger: {$key}.";
        } else {
            $passes++;
        }
    }
    $counts = $inventory['counts'] ?? [];
    $effective = $counts['effective_inherited_operations']
        ?? ($inventory['package_contract_statistics']['effective_inherited_crud_wire_bindings'] ?? null);
    $phantoms = $counts['phantom_inherited_operations']
        ?? ($inventory['package_contract_statistics']['phantom_inherited_operations'] ?? null);
    checkSame('effective inherited operation count', $capabilities['effective_count'], $effective, $errors, $passes);
    checkSame('phantom inherited operation count', 0, $capabilities['phantom_count'], $errors, $passes);
    checkSame('inventory phantom inherited operation count', 0, $phantoms, $errors, $passes);
    checkSame('capability override coverage', $capabilities['resource_count'], $capabilities['override_count'], $errors, $passes);
}

function verifyCoverage(array $inventory, array $official, array $package, array $capabilities, array $source, array &$errors, int &$passes): void
{
    $coverage = $inventory['source_coverage'] ?? [];
    $checks = [
        'official_openapi_operations_classified' => $official['declaration_count'],
        'official_openapi_operations_total'      => $official['declaration_count'],
        'package_resource_files_inspected'       => $capabilities['resource_count'],
        'package_resource_files_total'           => $capabilities['resource_count'],
        'package_operations_classified'          => count($package['keys']),
        'package_operations_total'               => count($package['keys']),
        'src_files_inspected'                    => $source['file_count'],
    ];
    foreach ($checks as $field => $expected) {
        if ($field === 'src_files_inspected' && ! array_key_exists($field, $coverage)) {
            $field = 'src_php_files_inspected';
            $expected = $source['php_file_count'];
        }
        checkSame("source_coverage.{$field}", $expected, $coverage[$field] ?? null, $errors, $passes);
    }

    $resourceSources = [];
    foreach (($inventory['package_resources'] ?? []) as $resource) {
        if (is_array($resource) && is_string($resource['source'] ?? null)) {
            $resourceSources[] = $resource['source'];
        }
    }
    sort($resourceSources, SORT_STRING);
    $expectedSources = $capabilities['resource_sources'];
    sort($expectedSources, SORT_STRING);
    checkSame('package resource source coverage', $expectedSources, $resourceSources, $errors, $passes);
}

function compareSets(string $label, array $expected, array $actual, array &$errors): void
{
    $missing = array_keys(array_diff_key($expected, $actual));
    $extra = array_keys(array_diff_key($actual, $expected));
    if ($missing !== [] || $extra !== []) {
        $errors[] = "{$label} mismatch; missing=".json_encode($missing).', extra='.json_encode($extra).'.';
    }
}

function checkSame(string $label, mixed $expected, mixed $actual, array &$errors, int &$passes): void
{
    if ($expected === $actual) {
        $passes++;

        return;
    }
    $errors[] = "{$label}: expected ".json_encode($expected, JSON_UNESCAPED_SLASHES).', got '.json_encode($actual, JSON_UNESCAPED_SLASHES).'.';
}

function firstValue(array $data, array $paths): mixed
{
    foreach ($paths as $path) {
        $value = $data;
        $found = true;
        foreach ($path as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                $found = false;
                break;
            }
            $value = $value[$segment];
        }
        if ($found) {
            return $value;
        }
    }

    return null;
}

/** @return array{stable:int,beta:int,preview:int} */
function normalizeStageCounts(array $counts): array
{
    $normalized = ['stable' => 0, 'beta' => 0, 'preview' => 0];
    foreach ($counts as $stage => $count) {
        $key = strtolower((string) $stage);
        if (in_array($key, ['ga', 'stable ga'], true)) {
            $key = 'stable';
        }
        if (array_key_exists($key, $normalized)) {
            $normalized[$key] = (int) $count;
        }
    }

    return $normalized;
}

function formatStageCounts(array $counts): string
{
    return "stable={$counts['stable']}, beta={$counts['beta']}, preview={$counts['preview']}";
}
