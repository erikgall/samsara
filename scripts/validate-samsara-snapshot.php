<?php

/**
 * Validate a dated Samsara OpenAPI snapshot and its provenance sidecar.
 *
 * This script uses only the PHP standard library. It verifies raw-byte
 * integrity, metadata consistency, and minimal OpenAPI structure. It does not
 * claim semantic conformance with Samsara's live API.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
try {
    $options = parseArguments(array_slice($argv, 1));

    if ($options['help']) {
        fwrite(STDOUT, usage());
        exit(0);
    }

    $snapshot = $options['snapshot'] ?? discoverSnapshot($options['directory']);
    $metadata = $options['metadata'] ?? discoverMetadata($snapshot);
    $result = validateSnapshot($snapshot, $metadata);

    fwrite(STDOUT, "Snapshot verified: {$result['snapshot']}\n");
    fwrite(STDOUT, "Metadata verified: {$result['metadata']}\n");
    fwrite(STDOUT, "SHA-256: {$result['sha256']}\n");
    fwrite(STDOUT, "OpenAPI version: {$result['openapi_version']}\n");
    fwrite(STDOUT, "Document version: {$result['document_version']}\n");
    fwrite(STDOUT, "Paths: {$result['path_count']}\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Validation failed: {$exception->getMessage()}\n");
    exit(1);
}

/**
 * @param  list<string>  $arguments
 * @return array{directory: string, snapshot: ?string, metadata: ?string, help: bool}
 */
function parseArguments(array $arguments): array
{
    $repositoryRoot = dirname(__DIR__);
    $options = [
        'directory' => $repositoryRoot.'/contracts/samsara',
        'snapshot'  => null,
        'metadata'  => null,
        'help'      => false,
    ];
    $seenOptions = [];

    for ($index = 0; $index < count($arguments); $index++) {
        $argument = $arguments[$index];

        if ($argument === '--help' || $argument === '-h') {
            $options['help'] = true;

            continue;
        }

        if (! in_array($argument, ['--directory', '--snapshot', '--metadata'], true)) {
            throw new RuntimeException("Unknown argument: {$argument}\n\n".usage());
        }

        if (isset($seenOptions[$argument])) {
            throw new RuntimeException("Duplicate option: {$argument}");
        }

        $value = $arguments[$index + 1] ?? null;

        if ($value === null || str_starts_with($value, '--')) {
            throw new RuntimeException("Missing value for {$argument}.\n\n".usage());
        }

        $options[substr($argument, 2)] = $value;
        $seenOptions[$argument] = true;
        $index++;
    }

    if (isset($seenOptions['--directory'], $seenOptions['--snapshot'])) {
        throw new RuntimeException('--directory and --snapshot cannot be combined.');
    }

    if (isset($seenOptions['--metadata']) && ! isset($seenOptions['--snapshot'])) {
        throw new RuntimeException('--metadata cannot be combined with automatic snapshot discovery; pass --snapshot.');
    }

    return $options;
}

function usage(): string
{
    return <<<'TEXT'
Usage:
  php scripts/validate-samsara-snapshot.php
  php scripts/validate-samsara-snapshot.php --snapshot PATH [--metadata PATH]
  php scripts/validate-samsara-snapshot.php --directory PATH

Without --snapshot, the validator selects the uniquely newest dated JSON file
in contracts/samsara (or --directory). Metadata defaults to a matching
<snapshot>.metadata.json sidecar. The earlier <snapshot-stem>.metadata.json form
is also recognized.

TEXT;
}

function discoverSnapshot(string $directory): string
{
    if (! is_dir($directory)) {
        throw new RuntimeException("Snapshot directory does not exist: {$directory}");
    }

    $snapshotsByDate = [];

    $iterator = new DirectoryIterator($directory);

    foreach ($iterator as $entry) {
        $filename = $entry->getFilename();

        if (str_ends_with($filename, '.metadata.json')) {
            continue;
        }

        $date = snapshotDate($filename);

        if ($date === null) {
            continue;
        }

        if ($entry->isLink()) {
            throw new RuntimeException("Snapshot file must not be a symbolic link: {$entry->getPathname()}");
        }

        if (! $entry->isFile()) {
            continue;
        }

        $snapshotsByDate[$date][] = $entry->getPathname();
    }

    if ($snapshotsByDate === []) {
        throw new RuntimeException("No dated JSON snapshots found in: {$directory}");
    }

    ksort($snapshotsByDate);
    $newestDate = array_key_last($snapshotsByDate);
    $newestSnapshots = $snapshotsByDate[$newestDate];

    if (count($newestSnapshots) !== 1) {
        sort($newestSnapshots);
        throw new RuntimeException(
            "Multiple snapshots have the newest date {$newestDate}; pass --snapshot explicitly: ".
            implode(', ', $newestSnapshots),
        );
    }

    return $newestSnapshots[0];
}

function snapshotDate(string $filename): ?string
{
    $matches = [];
    preg_match_all('/(?<!\d)(\d{4})-(\d{2})-(\d{2})(?!\d)/', $filename, $matches, PREG_SET_ORDER);

    if (count($matches) !== 1) {
        return null;
    }

    $year = (int) $matches[0][1];
    $month = (int) $matches[0][2];
    $day = (int) $matches[0][3];

    if (! checkdate($month, $day, $year)) {
        return null;
    }

    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

function discoverMetadata(string $snapshot): string
{
    $candidates = [];

    if (str_ends_with($snapshot, '.json')) {
        $candidates[] = substr($snapshot, 0, -5).'.metadata.json';
    }

    $candidates[] = $snapshot.'.metadata.json';
    $existing = array_values(array_filter(array_unique($candidates), is_file(...)));

    if (count($existing) === 1) {
        return $existing[0];
    }

    if (count($existing) > 1) {
        throw new RuntimeException('Multiple metadata sidecars match the snapshot; pass --metadata explicitly.');
    }

    throw new RuntimeException(
        'Metadata sidecar not found. Expected one of: '.implode(', ', $candidates),
    );
}

/**
 * @return array{
 *     snapshot: string,
 *     metadata: string,
 *     sha256: string,
 *     openapi_version: string,
 *     document_version: string,
 *     path_count: int
 * }
 */
function validateSnapshot(string $snapshot, string $metadata): array
{
    assertReadableFile($snapshot, 'Snapshot');
    assertReadableFile($metadata, 'Metadata');

    if (snapshotDate(basename($snapshot)) === null) {
        throw new RuntimeException('Snapshot filename must contain exactly one valid YYYY-MM-DD date.');
    }

    $snapshotBytes = file_get_contents($snapshot);
    $metadataBytes = file_get_contents($metadata);

    if ($snapshotBytes === false || $snapshotBytes === '') {
        throw new RuntimeException("Snapshot is empty or unreadable: {$snapshot}");
    }

    if ($metadataBytes === false || $metadataBytes === '') {
        throw new RuntimeException("Metadata is empty or unreadable: {$metadata}");
    }

    $snapshotDocument = decodeJsonObject($snapshotBytes, 'Snapshot');
    $metadataDocument = decodeJsonObject($metadataBytes, 'Metadata');
    $openApiVersion = requireNonEmptyStringProperty($snapshotDocument, 'openapi', 'Snapshot');

    if (preg_match('/^3\.\d+\.\d+$/', $openApiVersion) !== 1) {
        throw new RuntimeException('Snapshot field openapi must be an OpenAPI 3 semantic version such as 3.0.1.');
    }

    $info = requireObjectProperty($snapshotDocument, 'info', 'Snapshot');
    $documentVersion = requireNonEmptyStringProperty($info, 'version', 'Snapshot info');
    requireNonEmptyStringProperty($info, 'title', 'Snapshot info');
    $paths = requireObjectProperty($snapshotDocument, 'paths', 'Snapshot');

    if (property_exists($snapshotDocument, 'components') && ! is_object($snapshotDocument->components)) {
        throw new RuntimeException('Snapshot field components must be an object when present.');
    }

    validateMetadata(
        $metadataDocument,
        $snapshot,
        $snapshotBytes,
        $openApiVersion,
        $documentVersion,
    );

    return [
        'snapshot'         => realpath($snapshot) ?: $snapshot,
        'metadata'         => realpath($metadata) ?: $metadata,
        'sha256'           => hash('sha256', $snapshotBytes),
        'openapi_version'  => $openApiVersion,
        'document_version' => $documentVersion,
        'path_count'       => count(get_object_vars($paths)),
    ];
}

function assertReadableFile(string $path, string $label): void
{
    if (is_link($path)) {
        throw new RuntimeException("{$label} file must not be a symbolic link: {$path}");
    }

    if (! is_file($path) || ! is_readable($path)) {
        throw new RuntimeException("{$label} file is not readable: {$path}");
    }
}

function decodeJsonObject(string $json, string $label): object
{
    try {
        $document = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        throw new RuntimeException("{$label} is not valid JSON: {$exception->getMessage()}");
    }

    if (! is_object($document)) {
        throw new RuntimeException("{$label} JSON root must be an object.");
    }

    return $document;
}

function validateMetadata(
    object $metadata,
    string $snapshot,
    string $snapshotBytes,
    string $openApiVersion,
    string $documentVersion,
): void {
    if (property_exists($metadata, 'schema_version') && $metadata->schema_version !== 1) {
        throw new RuntimeException('Metadata field schema_version must equal 1.');
    }

    $metadataSnapshot = requireNonEmptyStringProperty($metadata, 'snapshot', 'Metadata');
    assertMetadataPathMatchesSnapshot($metadataSnapshot, $snapshot);

    $sourceUrl = requireNonEmptyStringProperty($metadata, 'source_url', 'Metadata');
    assertHttpsUrl($sourceUrl, 'Metadata field source_url');

    requireTimestampProperty($metadata, 'retrieved_at', 'Metadata');

    $timezone = requireNonEmptyStringProperty($metadata, 'retrieval_timezone', 'Metadata');

    try {
        new DateTimeZone($timezone);
    } catch (Exception) {
        throw new RuntimeException('Metadata field retrieval_timezone must be a valid timezone identifier.');
    }

    if (property_exists($metadata, 'retrieval')) {
        $retrieval = requireObjectProperty($metadata, 'retrieval', 'Metadata');
        requireNonEmptyStringProperty($retrieval, 'command', 'Metadata retrieval');
    }

    validateHttpMetadata($metadata);
    validateAuthorityMetadata($metadata);
    validateLicenseMetadata($metadata);

    $upstream = requireObjectProperty($metadata, 'upstream', 'Metadata');
    requireNullableStringProperty($upstream, 'commit', 'Metadata upstream');

    $digest = strtolower(requireNonEmptyStringProperty($metadata, 'sha256', 'Metadata'));

    if (preg_match('/^[a-f0-9]{64}$/', $digest) !== 1) {
        throw new RuntimeException('Metadata field sha256 must contain 64 hexadecimal characters.');
    }

    $actualDigest = hash('sha256', $snapshotBytes);

    if (! hash_equals($digest, $actualDigest)) {
        throw new RuntimeException("SHA-256 mismatch: metadata has {$digest}, snapshot has {$actualDigest}.");
    }

    $byteSize = $metadata->raw_bytes ?? null;

    if (! is_int($byteSize) || $byteSize < 1) {
        throw new RuntimeException('Metadata field raw_bytes must be a positive integer.');
    }

    if ($byteSize !== strlen($snapshotBytes)) {
        throw new RuntimeException(
            "Byte-size mismatch: metadata has {$byteSize}, snapshot has ".strlen($snapshotBytes).'.',
        );
    }

    $metadataOpenApiVersion = requireNonEmptyStringProperty($upstream, 'openapi', 'Metadata upstream');

    if ($metadataOpenApiVersion !== $openApiVersion) {
        throw new RuntimeException('Metadata OpenAPI version does not match the snapshot.');
    }

    $metadataDocumentVersion = requireNonEmptyStringProperty($upstream, 'info_version', 'Metadata upstream');

    if ($metadataDocumentVersion !== $documentVersion) {
        throw new RuntimeException('Metadata document version does not match snapshot info.version.');
    }

    $validation = requireObjectProperty($metadata, 'validator', 'Metadata');
    requireNonEmptyStringProperty($validation, 'name', 'Metadata validation');
    requireNonEmptyStringProperty($validation, 'version', 'Metadata validation');
    requireNonEmptyStringProperty($validation, 'command', 'Metadata validation');
    $validationResult = requireNonEmptyStringProperty($validation, 'result', 'Metadata validation');

    if (! in_array($validationResult, ['pass', 'valid'], true)) {
        throw new RuntimeException('Metadata validation field result must equal pass or valid.');
    }

    if (($validation->exit_code ?? null) !== 0) {
        throw new RuntimeException('Metadata validation field exit_code must equal 0.');
    }

    if (property_exists($validation, 'validated_at')) {
        requireTimestampProperty($validation, 'validated_at', 'Metadata validation');
    }

    if (property_exists($metadata, 'reference_resolution')) {
        $referenceResolution = requireObjectProperty($metadata, 'reference_resolution', 'Metadata');

        if (requireNonEmptyStringProperty($referenceResolution, 'result', 'Metadata reference resolution') !== 'pass') {
            throw new RuntimeException('Metadata reference resolution field result must equal pass.');
        }

        if (($referenceResolution->errors ?? null) !== 0) {
            throw new RuntimeException('Metadata reference resolution field errors must equal 0.');
        }
    }

    if (property_exists($metadata, 'predecessor')) {
        validatePredecessor($metadata);
    }
}

function validateHttpMetadata(object $metadata): void
{
    $http = requireObjectProperty($metadata, 'http', 'Metadata');

    if (! is_int($http->status ?? null) || $http->status < 100 || $http->status > 599) {
        throw new RuntimeException('Metadata HTTP field status must be an integer from 100 through 599.');
    }

    requireNonEmptyStringProperty($http, 'date', 'Metadata HTTP');
    requireNonEmptyStringProperty($http, 'content_type', 'Metadata HTTP');
    requireNullableStringProperty($http, 'etag', 'Metadata HTTP');
    requireNullableStringProperty($http, 'last_modified', 'Metadata HTTP');
}

function validateAuthorityMetadata(object $metadata): void
{
    $authority = requireObjectProperty($metadata, 'authority', 'Metadata');
    $guideUrl = requireNonEmptyStringProperty($authority, 'guide_url', 'Metadata authority');
    $catalogUrl = requireNonEmptyStringProperty($authority, 'api_catalog_url', 'Metadata authority');
    assertHttpsUrl($guideUrl, 'Metadata authority field guide_url');
    assertHttpsUrl($catalogUrl, 'Metadata authority field api_catalog_url');
}

function validateLicenseMetadata(object $metadata): void
{
    $license = requireObjectProperty($metadata, 'license', 'Metadata');
    requireNullableStringProperty($license, 'declared_in_contract', 'Metadata license');
    requireNullableStringProperty($license, 'terms_of_service_in_contract', 'Metadata license');
    requireNonEmptyStringProperty($license, 'note', 'Metadata license');
    $termsUrl = requireNullableStringProperty($license, 'website_terms_url', 'Metadata license');

    if ($termsUrl !== null) {
        assertHttpsUrl($termsUrl, 'Metadata license field website_terms_url');
    }
}

function validatePredecessor(object $metadata): void
{
    if (($metadata->predecessor ?? null) === null) {
        return;
    }

    if (! is_object($metadata->predecessor)) {
        throw new RuntimeException('Metadata field predecessor must be an object or null.');
    }

    $snapshot = requireNonEmptyStringProperty($metadata->predecessor, 'snapshot', 'Metadata predecessor');
    normalizeSafeRelativePath($snapshot, 'Metadata predecessor field snapshot');
    $digest = strtolower(requireNonEmptyStringProperty($metadata->predecessor, 'sha256', 'Metadata predecessor'));

    if (preg_match('/^[a-f0-9]{64}$/', $digest) !== 1) {
        throw new RuntimeException('Metadata predecessor field sha256 must contain 64 hexadecimal characters.');
    }
}

function assertMetadataPathMatchesSnapshot(string $metadataSnapshot, string $snapshot): void
{
    $normalizedMetadataSnapshot = normalizeSafeRelativePath(
        $metadataSnapshot,
        'Metadata field snapshot',
    );
    $snapshotPath = str_replace('\\', '/', realpath($snapshot) ?: $snapshot);

    if (! str_contains($normalizedMetadataSnapshot, '/')) {
        if ($normalizedMetadataSnapshot === basename($snapshotPath)) {
            return;
        }

        throw new RuntimeException('Metadata field snapshot must identify the validated snapshot path.');
    }

    if (str_ends_with($snapshotPath, '/'.$normalizedMetadataSnapshot)) {
        return;
    }

    throw new RuntimeException('Metadata field snapshot must identify the validated snapshot path.');
}

function normalizeSafeRelativePath(string $path, string $label): string
{
    $normalizedPath = str_replace('\\', '/', trim($path));
    $segments = explode('/', $normalizedPath);

    if ($normalizedPath === '' || str_starts_with($normalizedPath, '/') || preg_match('/^[A-Za-z]:\//', $normalizedPath) === 1) {
        throw new RuntimeException("{$label} must be a safe repository-relative path or basename.");
    }

    if (in_array('', $segments, true) || in_array('.', $segments, true) || in_array('..', $segments, true)) {
        throw new RuntimeException("{$label} must be a safe repository-relative path or basename.");
    }

    return $normalizedPath;
}

function requireNullableStringProperty(object $document, string $property, string $label): ?string
{
    if (! property_exists($document, $property)) {
        throw new RuntimeException("{$label} field {$property} must be present; use null when unavailable.");
    }

    if ($document->{$property} === null) {
        return null;
    }

    if (! is_string($document->{$property})) {
        throw new RuntimeException("{$label} field {$property} must be a string or null.");
    }

    return $document->{$property};
}

function assertHttpsUrl(string $url, string $label): void
{
    $parts = parse_url($url);

    if (! is_array($parts) || ($parts['scheme'] ?? null) !== 'https' || ! isset($parts['host'])) {
        throw new RuntimeException("{$label} must be an absolute HTTPS URL.");
    }
}

function requireObjectProperty(object $document, string $property, string $label): object
{
    if (! property_exists($document, $property) || ! is_object($document->{$property})) {
        throw new RuntimeException("{$label} field {$property} must be an object.");
    }

    return $document->{$property};
}

function requireNonEmptyStringProperty(object $document, string $property, string $label): string
{
    if (! property_exists($document, $property) || ! is_string($document->{$property})) {
        throw new RuntimeException("{$label} field {$property} must be a non-empty string.");
    }

    $value = trim($document->{$property});

    if ($value === '') {
        throw new RuntimeException("{$label} field {$property} must be a non-empty string.");
    }

    return $value;
}

function requireTimestampProperty(object $document, string $property, string $label): string
{
    $timestamp = requireNonEmptyStringProperty($document, $property, $label);
    $matches = [];

    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $timestamp, $matches) !== 1) {
        throw new RuntimeException("{$label} field {$property} must be an RFC 3339 timestamp with a timezone.");
    }

    $validDate = checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    $validTime = (int) $matches[4] <= 23 && (int) $matches[5] <= 59 && (int) $matches[6] <= 59;

    if (! $validDate || ! $validTime) {
        throw new RuntimeException("{$label} field {$property} is not a valid calendar timestamp.");
    }

    if ($matches[7] !== 'Z') {
        $offsetHour = (int) substr($matches[7], 1, 2);
        $offsetMinute = (int) substr($matches[7], 4, 2);

        if ($offsetHour > 23 || $offsetMinute > 59) {
            throw new RuntimeException("{$label} field {$property} is not a valid RFC 3339 offset.");
        }
    }

    try {
        new DateTimeImmutable($timestamp);
    } catch (Exception) {
        throw new RuntimeException("{$label} field {$property} is not a valid calendar timestamp.");
    }

    return $timestamp;
}
