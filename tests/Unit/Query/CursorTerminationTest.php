<?php

namespace Samsara\Tests\Unit\Query;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\SamsaraException;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * @author Erik Galloway <erik@erikgall.com>
 */
class CursorTerminationTest extends TestCase
{
    /**
     * @param  list<string>  $cursors
     */
    #[Test]
    #[DataProvider('repeatedCursors')]
    public function it_rejects_repeated_cursors(
        string $terminal,
        array $cursors,
        bool $empty,
        ?string $initialCursor,
        string $cursorParameter,
        bool $useFilter,
    ): void {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $sequence = $http->sequence();

        foreach ($cursors as $index => $cursor) {
            $sequence->push([
                'data'       => $empty ? [] : [['id' => (string) $index]],
                'pagination' => ['endCursor' => $cursor, 'hasNextPage' => true],
            ]);
        }

        // Bound the request count even if the cursor guard regresses.
        $sequence->push(['data' => []]);
        $http->fake(['*' => $sequence]);
        $builder = (new Samsara('test-token', ['attempts' => 1], $http))
            ->vehicles()->query()->withCursorParameter($cursorParameter);

        if ($initialCursor !== null) {
            $useFilter
                ? $builder->where($cursorParameter, $initialCursor)
                : $builder->after($initialCursor);
        }

        try {
            $this->consume($builder, $terminal);
            $this->fail('Expected repeated cursor rejection.');
        } catch (SamsaraException $exception) {
            $this->assertSame(SamsaraException::class, $exception::class);
            $this->assertSame([
                'endpoint' => '/fleet/vehicles',
                'cursor'   => $cursors[array_key_last($cursors)],
            ], $exception->getContext());
            $http->assertSentCount(count($cursors));
        }
    }

    #[Test]
    #[DataProvider('terminals')]
    public function it_terminates_after_an_empty_page_and_a_terminal_repeated_cursor(string $terminal): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->sequence()
            ->push(['data' => [], 'pagination' => ['endCursor' => 'A', 'hasNextPage' => true]])
            ->push(['data' => [['id' => 'last']], 'pagination' => ['endCursor' => 'A', 'hasNextPage' => false]])]);
        $builder = (new Samsara('test-token', ['attempts' => 1], $http))->vehicles()->query();

        $this->assertSame(['last'], $this->consume($builder, $terminal));
        $http->assertSentCount(2);
    }

    /**
     * @return iterable<string, array{string, list<string>, bool, string|null, string, bool}>
     */
    public static function repeatedCursors(): iterable
    {
        $scenarios = [
            'nonempty pages'       => [['A', 'A'], false, null, 'after', false],
            'empty pages'          => [['A', 'A'], true, null, 'after', false],
            'cycle'                => [['A', 'B', 'A'], false, null, 'after', false],
            'initial cursor'       => [['A'], false, 'A', 'after', false],
            'initial cursor cycle' => [['B', 'A'], false, 'A', 'after', false],
            'custom cursor'        => [['A'], true, 'A', 'startingAfter', false],
            'cursor filter'        => [['A'], false, 'A', 'after', true],
            'custom cursor filter' => [['A'], true, 'A', 'startingAfter', true],
        ];

        foreach (self::terminals() as [$terminal]) {
            foreach ($scenarios as $scenario => $arguments) {
                yield $terminal.' '.$scenario => [$terminal, ...$arguments];
            }
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function terminals(): array
    {
        return [
            'lazy'      => ['lazy'],
            'each'      => ['each'],
            'collect'   => ['collect'],
            'paginator' => ['paginator'],
        ];
    }

    /**
     * @return list<string|null>
     */
    private function consume(Builder $builder, string $terminal): array
    {
        if ($terminal === 'paginator') {
            $ids = [];
            $page = $builder->paginate();

            do {
                $ids = [...$ids, ...$page->items()->ids()];
                $page = $page->nextPage();
            } while ($page !== null);

            return $ids;
        }

        $ids = $builder->lazy()->map(fn (Entity $entity): ?string => $entity->getId());

        return match ($terminal) {
            'lazy'    => $ids->all(),
            'collect' => $ids->collect()->all(),
            'each'    => $this->consumeEach($ids),
        };
    }

    /**
     * @param  LazyCollection<int, string|null>  $collection
     * @return list<string|null>
     */
    private function consumeEach(LazyCollection $collection): array
    {
        $ids = [];
        $collection->each(function (?string $id) use (&$ids): void {
            $ids[] = $id;
        });

        return $ids;
    }
}
