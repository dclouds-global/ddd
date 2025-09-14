<?php

declare(strict_types=1);

namespace Tests\Aggregate;

use Dclouds\Ddd\Aggregate\AbstractAggregateRoot;
use Dclouds\Ddd\Aggregate\AbstractEvent;
use Dclouds\Ddd\Contracts\Aggregate\DomainEventInterface;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\DomainEventNotImplementedException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToChangeVersionForUnmodifiedAggregateException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseIntegrationEventsException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseNonIntegrationEventsException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\IncorrectAggregateVersionException;
use Dclouds\Ddd\ValueObjects\Uuid\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FooEvent extends AbstractEvent
{
    public function __setVersion(int $version): void
    {
        $this->eventVersion = $version;
    }

    public function __setAggregateId(Uuid $aggregateId): void
    {
        $this->aggregateId = $aggregateId;
    }
}
final class BarEvent extends AbstractEvent
{
}

final class FooBarEvent implements DomainEventInterface
{
    private(set) ?Uuid $aggregateId = null;
    private(set) Uuid $eventId {
        get {
            return $this->eventId ??= Uuid::generate();
        }
        set => $value;
    }
    private(set) int $eventVersion = 1;
    private(set) string $eventType = 'FooBarEvent';
}

final class Foo extends AbstractAggregateRoot
{
    public function __construct(protected(set) Uuid $id, protected(set) int $var = 1)
    {
    }

    public function applyFooEvent(FooEvent $event): void
    {
        $this->var = 2;
    }

    public function applyFooEventV2(FooEvent $event): void
    {
        $this->var = 10;
    }

    public function applyFooBarEvent(FooBarEvent $event): void
    {
        $this->var = 20;
    }

    protected function validate(): void
    {
    }
}

#[CoversClass(AbstractAggregateRoot::class)]
final class AbstractAggregateRootTest extends TestCase
{
    /**
     * Эксземпляр агрегата создается корректно
     *
     * @return void
     */
    public function testCase1(): void
    {
        //when
        $aggregate = new Foo(Uuid::generate());

        //then
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/i',
            $aggregate->id->asString()
        );
    }

    /**
     * Применение события отрабатывает корректно, когда для данного
     * события существует метод реализации. При этом само событие успешно
     * сохраняется в массиев событий агрегата, а свойство `aggregateId`
     * принимает значение, соответствующее идентификатору агрегата.
     *
     * @return void
     */
    public function testCase2(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $event = new FooEvent();
        $event2 = new FooEvent();
        $event2->__setVersion(2);

        //when
        $aggregate->apply($event);
        $firstVar = $aggregate->var;
        $aggregate->apply($event2);
        $secondVar = $aggregate->var;
        $aggregate->version = 1;

        //then
        $this->assertCount(2, $aggregate->releaseDomainEvents());
        $this->assertEquals(2, $firstVar);
        $this->assertEquals(10, $secondVar);
    }

    /**
     * В случае, если для события в рамках агрегата нет метода реализации
     * будет выброшено исключение
     *
     * @param DomainEventInterface $event Событие предметной области
     * @return void
     */
    #[DataProvider('case3DataProvider')]
    public function testCase3(DomainEventInterface $event): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());

        //expect
        $this->expectException(DomainEventNotImplementedException::class);

        //when
        $aggregate->apply($event);
    }

    /**
     * @return array<string, array<string, DomainEventInterface>>
     */
    public static function case3DataProvider(): array
    {
        $vEvent = new FooEvent();
        $vEvent->__setVersion(3);
        return [
            'Event without method for name' => ['event' => new BarEvent()],
            'Event without method for version' => ['event' => $vEvent],
        ];
    }

    /**
     * Агрегат помечается как модифицированный только после того,
     * как к нему было применено какое-либо событие.
     */
    public function testCase4(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $event = new FooEvent();

        //when
        $stateBeforeUpdate = $aggregate->isModified;
        $aggregate->apply($event);
        $stateAfterUpdate = $aggregate->isModified;

        //then
        $this->assertFalse($stateBeforeUpdate);
        $this->assertTrue($stateAfterUpdate);
    }

    /**
     * Версия агрегата не может быть изменена, если к нему
     * не было применено никаких событий.
     */
    public function testCase5(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());

        //expect
        $this->expectException(ImpossibleToChangeVersionForUnmodifiedAggregateException::class);

        //when
        $aggregate->version = 2;
    }

    /**
     * Новая версия агрегата не может быть меньше, или равна текущей версии
     */
    #[DataProvider('case6DataProvider')]
    public function testCase6(int $ver): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $aggregate->apply(new FooEvent());
        $aggregate->version = 1;

        //expect
        $this->expectException(IncorrectAggregateVersionException::class);

        //when
        $aggregate->version = $ver;
    }

    /**
     * @return array<string, array<string, int>>
     */
    public static function case6DataProvider(): array
    {
        return [
            'new version less than current' => ['ver' => 0],
            'new version equals current' => ['ver' => 1],
        ];
    }

    /**
     * Нельзя выпустить события предметной области, пока не
     * была обновлена версия агрегата
     */
    public function testCase7(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $aggregate->apply(new FooEvent());

        //expect
        $this->expectException(ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException::class);

        //when
        $aggregate->releaseDomainEvents();
    }

    /**
     * Флаг модицикации не будет изменен, если передаваемое событие
     * содержит идентификатор агрегата.
     */
    public function testCase8(): void
    {
        //given
        $uuid = Uuid::generate();
        $aggregate = new Foo($uuid);
        $event = new FooEvent();
        $event->__setAggregateId($uuid);

        //when
        $aggregate->apply($event);

        //then
        $this->assertFalse($aggregate->isModified);
    }

    /**
     * При выпуске событий предметной области их внутренняя
     * очередь очищается, а ее события переносятся во внутреннюю
     * очередь интеграционных событий.
     */
    public function testCase9(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $aggregate->apply(new FooEvent());
        $aggregate->version = 1;

        //when
        $releasedEvents = $aggregate->releaseDomainEvents();

        //then
        $this->assertCount(1, $releasedEvents);
        $this->assertInstanceOf(FooEvent::class, $releasedEvents[0]);
        $integrationEvents = $aggregate->releaseIntegrationEvents();
        $this->assertCount(1, $integrationEvents);
        $this->assertInstanceOf(FooEvent::class, $integrationEvents[0]);
    }

    /**
     * нельзя произвести выпуск интеграционных событий до выпуска событий
     * предметной области
     */
    public function testCase10(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $aggregate->apply(new FooEvent());
        $aggregate->version = 1;

        //expect
        $this->expectException(ImpossibleToReleaseIntegrationEventsException::class);

        //when
        $aggregate->releaseIntegrationEvents();
    }

    /**
     * При выпуске интеграционных событий, каждое событие приобретает
     * значение версии агрегата
     */
    public function testCase11(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $aggregate->apply(new FooEvent());
        $aggregate->version = 5;
        $aggregate->releaseDomainEvents();

        //when
        $releasedIntegrationEvents = $aggregate->releaseIntegrationEvents();

        //then
        $this->assertCount(1, $releasedIntegrationEvents);
        $this->assertInstanceOf(FooEvent::class, $releasedIntegrationEvents[0]);
        $this->assertEquals(5, $releasedIntegrationEvents[0]->aggregateVersion);
    }

    /**
     * Нельзя выпустить события, которые не являются интеграционными
     */
    public function testCase12(): void
    {
        //given
        $aggregate = new Foo(Uuid::generate());
        $aggregate->apply(new FooBarEvent());
        $aggregate->version = 5;
        $aggregate->releaseDomainEvents();

        //expect
        $this->expectException(ImpossibleToReleaseNonIntegrationEventsException::class);

        //when
        $aggregate->releaseIntegrationEvents();
    }
}
