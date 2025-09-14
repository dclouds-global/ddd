<?php

declare(strict_types=1);

namespace Tests\Aggregate;

use Dclouds\Ddd\Aggregate\AbstractEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

class FooTestEvent extends AbstractEvent
{
}

class BarTestEvent extends AbstractEvent
{
    public int $eventVersion = 2;
}

#[CoversClass(AbstractEvent::class)]
class AbstractEventTest extends TestCase
{
    /**
     * Классы наследники AbstractEvent корректно устанавливают
     * и возвращают свою версию, тип, идентификатор, версию агрегата
     * и его идентификатор
     */
    public function testCase1(): void
    {
        //when
        $foo = new FooTestEvent();

        //then
        $this->assertEquals(1, $foo->eventVersion);
        $this->assertEquals('FooTestEvent', $foo->eventType);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/i',
            $foo->eventId->asString()
        );
        $this->assertNull($foo->aggregateId);
        $this->assertNull($foo->aggregateVersion);
    }

    /**
     * Версия события может быть изменена, при выставлении значения
     * для соответствующего свойства класса
     */
    public function testCase2(): void
    {
        //when
        $foo = new BarTestEvent();

        //then
        $this->assertEquals(2, $foo->eventVersion);
    }
}
