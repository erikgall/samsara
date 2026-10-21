<?php

namespace Samsara\Tests\Unit\Data;

use ReflectionClass;
use FilesystemIterator;
use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Shared construction and serialization contract for every DTO.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EntityContractTest extends TestCase
{
    /**
     * @param  class-string<Entity>  $class
     */
    #[Test]
    #[DataProvider('entityClasses')]
    public function it_round_trips_raw_attributes_for_every_dto(string $class): void
    {
        $attributes = [
            'id'           => 'dto-1',
            'name'         => 'Example',
            'unknownField' => ['zero' => 0, 'enabled' => false, 'missing' => null, 'list' => ['a', 'b']],
        ];

        $entity = new $class($attributes);
        $made = $class::make($attributes);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertInstanceOf($class, $made);
        $this->assertSame($attributes, $entity->toArray());
        $this->assertSame($attributes, $made->toArray());
        $this->assertEquals($entity, $made);
    }

    /**
     * @return iterable<string, array{class-string<Entity>}>
     */
    public static function entityClasses(): iterable
    {
        $directory = dirname(__DIR__, 3).'/src/Data/';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = 'Samsara\\Data\\'.str_replace('/', '\\', substr($file->getPathname(), strlen($directory), -4));
            if (is_subclass_of($class, Entity::class) && (new ReflectionClass($class))->isInstantiable()) {
                yield $class => [$class];
            }
        }
    }
}
