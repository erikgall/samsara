<?php

namespace Samsara\Http;

/**
 * Prepare query values for Laravel's HTTP client.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
final class QueryString
{
    /**
     * Prepare standard and form/explode=true values for one GET request.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string>  $repeatedParameters
     * @return array{string, array<string, mixed>}
     */
    public static function prepare(
        string $endpoint,
        array $query,
        array $repeatedParameters = [],
    ): array {
        $pairs = [];

        foreach ($repeatedParameters as $parameter) {
            $values = $query[$parameter] ?? null;

            if (! is_array($values)) {
                continue;
            }

            unset($query[$parameter]);

            foreach ($values as $value) {
                $pairs[] = rawurlencode($parameter).'='.rawurlencode(self::stringify($value));
            }
        }

        foreach ($query as $parameter => $value) {
            if (is_bool($value)) {
                $query[$parameter] = self::stringify($value);
            }
        }

        if ($pairs === []) {
            return [$endpoint, $query];
        }

        $standardQuery = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        if ($standardQuery !== '') {
            $pairs[] = $standardQuery;
        }

        return [
            $endpoint.(str_contains($endpoint, '?') ? '&' : '?').implode('&', $pairs),
            [],
        ];
    }

    /**
     * Serialize one scalar query value using OpenAPI-compatible booleans.
     */
    private static function stringify(mixed $value): string
    {
        return match ($value) {
            true    => 'true',
            false   => 'false',
            default => (string) $value,
        };
    }
}
