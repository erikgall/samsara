# Querying, Responses, and Errors

## Build the Query Through the Resource

Begin with the helper that selects the intended endpoint:

```php
$drivers = Samsara::drivers()
    ->query()
    ->whereTag(['tag-1', 'tag-2'])
    ->get();

$history = Samsara::vehicleStats()
    ->history()
    ->whereVehicle('vehicle-1')
    ->types(['gps'])
    ->between($start, $end)
    ->get();
```

The builder is mutable. Apply filters before one terminal operation.

## Preserve Query Encoding

- `types()`, `expand()`, `withDecorations()`, `whereAttribute()`, `whereDriver()`, `whereParentTag()`, `whereTag()`, and `whereVehicle()` encode arrays as comma-separated values.
- Endpoint helpers call `repeatQueryParameters()` for upstream form/explode arrays that require repeated keys.
- Arrays passed directly to `where()` retain Laravel's bracket encoding.

Do not replace these helpers with hand-built query strings. When encoding matters, assert the request with `assertRequestedWithParams()` or inspect `getRecordedRequests()`.

## Choose the Terminal Operation Deliberately

| Terminal     | Result             | Behavior                                                   |
| ------------ | ------------------ | ---------------------------------------------------------- |
| `get()`      | `EntityCollection` | Reads one response page; it does not follow later cursors. |
| `first()`    | `?object`          | Applies a limit of one and returns the first mapped item.  |
| `paginate()` | `CursorPaginator`  | Reads one page and preserves cursor metadata.              |
| `lazy()`     | `LazyCollection`   | Walks pages until the API reports no next cursor.          |

The mapped item type comes from the selected resource or helper. Inspect the PHP return type, PHPDoc, resource page, and focused test before accessing fields or serializing the result. Some explicit helpers return raw arrays or scalars rather than mapped entities.

## Pagination Rules

The effective page size precedence is:

1. `paginate($perPage)` or `lazy($chunkSize)` argument.
2. `limit()`.
3. `where('limit', ...)`.
4. Configured `samsara.per_page`.

Some official cursor endpoints accept `after` but no `limit`. Their helpers call `withoutDefaultPageSize()`, so no configured default is sent. Preserve that endpoint-specific behavior.

Guard `nextPage()` because it returns `null` when no page remains:

```php
if ($page->hasMorePages()) {
    $nextPage = $page->nextPage();
}
```

## Handle SDK Exceptions

Catch exceptions from `Samsara\Exceptions`, not Laravel HTTP exceptions, around SDK operations:

- `AuthenticationException`: HTTP 401.
- `AuthorizationException`: HTTP 403.
- `ValidationException`: HTTP 400 or 422.
- `NotFoundException`: HTTP 404 for operations other than supported `find()`.
- `RateLimitException`: HTTP 429.
- `ServerException`: HTTP 5xx.
- `Illuminate\Http\Client\ConnectionException`: exhausted connection failure. The SDK's reserved `Samsara\Exceptions\ConnectionException` class is not thrown.
- `UnsupportedOperationException`: rejected locally before a request.
- `SamsaraException`: base type for remaining mapped SDK failures.

Use specific catches only when the application has distinct recovery behavior. Otherwise catch `SamsaraException` at the owning service boundary. A supported `find()` returning `null` on 404 is normal control flow.
