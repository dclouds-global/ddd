<?php

declare(strict_types=1);

namespace Tests\ValueObjects\Uuid;

use Dclouds\Ddd\ValueObjects\Uuid\Exceptions\FailedUuidCreationException;
use Dclouds\Ddd\ValueObjects\Uuid\Uuid;
use Dclouds\Ddd\ValueObjects\Uuid\Validator\UuidValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Uuid::class)]
final class UuidTest extends TestCase
{
    /**
     * Новый экземпляр успешно создается из строки в формате UUIDv4
     */
    public function testCase1(): void
    {
        //expect
        $this->expectNotToPerformAssertions();

        //when
        new Uuid('c5f808d2-3d80-4bc9-aff8-29703c9f1149');
    }

    /**
     * При попытке создать новый экземпляр из строки некорректного
     * формата выбрасывается исключение
     */
    #[DataProvider('case2DataProvider')]
    public function testCase2(string $uuid): void
    {
        //expect
        $this->expectException(FailedUuidCreationException::class);

        //when
        new Uuid($uuid);
    }

    /**
     * @return array<string, array<string>>
     */
    public static function case2DataProvider(): array
    {
        return [
            'admin' => ['513422ac-938b-11ef-b864-0242ac120002'],
            'manager' => ['0192c888-cc7b-7b28-852b-36ac17279ebb'],
            ['abc'],
        ];
    }

    /**
     * Скалярное значение типа должно соответствовать строке на основе которой
     * экземпляр данного типа был создан
     */
    public function testCase3(): void
    {
        //given
        $uuid = new Uuid('c5f808d2-3d80-4bc9-aff8-29703c9f1149');

        //then
        $this->assertEquals('c5f808d2-3d80-4bc9-aff8-29703c9f1149', $uuid->asString());
    }

    /**
     * Один экземпляр типа соответствует другому экземпляру, если они созданы
     * на основе одной и той же строки формата UUIDv4, а также соответствует
     * самой этой строке
     */
    public function testCase4(): void
    {
        //given
        $baseUuid = new Uuid('c5f808d2-3d80-4bc9-aff8-29703c9f1149');
        $comparableUuid = new Uuid('c5f808d2-3d80-4bc9-aff8-29703c9f1149');

        //then
        $this->assertTrue($baseUuid->isEqual($comparableUuid));
        $this->assertTrue($baseUuid->isEqual('c5f808d2-3d80-4bc9-aff8-29703c9f1149'));
    }

    /**
     * Один экземпляр типа не соответствует другому экземпляру, если они созданы
     * на основе разных строк формата UUIDv4, а также соответствует любым строкам
     * формата UUIDv4 за исключением той, на основе которой он был создан
     */
    public function testCase5(): void
    {
        //given
        $baseUuid = new Uuid('c5f808d2-3d80-4bc9-aff8-29703c9f1149');
        $comparableUuid = new Uuid('235ce576-d966-43f3-ae6a-333b26fd05ef');

        //then
        $this->assertTrue($baseUuid->isNotEqual($comparableUuid));
        $this->assertTrue($baseUuid->isNotEqual('3626133f-2942-4102-a524-39b2f4d11fbe'));
    }

    /**
     * Генерирует новое значение в корректном формате
     */
    public function testCase6(): void
    {
        //given
        $uuid = Uuid::generate();
        $validator = new UuidValidator($uuid->getTypeName());

        //expect
        $this->expectNotToPerformAssertions();

        //when
        $validator->thatIdIsValidUUIDv4String($uuid->asString());
    }

    /**
     * Значения генерируемые позднее по времени определяются как
     * более новые. Соответственно значения генерируемые раньше
     * по времени определяются как более старые
     */
    public function testCase7(): void
    {
        //given
        $first = Uuid::generate();
        $second = Uuid::generate();
        $third = Uuid::generate();
        $fourth = Uuid::generate();

        //then
        $this->assertTrue($first->isOlderThan($second));
        $this->assertTrue($first->isOlderThan($third));
        $this->assertTrue($first->isOlderThan($fourth));

        $this->assertTrue($second->isNewerThan($first));
        $this->assertTrue($second->isOlderThan($third));
        $this->assertTrue($second->isOlderThan($fourth));

        $this->assertTrue($third->isNewerThan($first));
        $this->assertTrue($third->isNewerThan($second));
        $this->assertTrue($third->isOlderThan($fourth));

        $this->assertTrue($fourth->isNewerThan($first));
        $this->assertTrue($fourth->isNewerThan($second));
        $this->assertTrue($fourth->isNewerThan($third));
    }

    /**
     * Можно сериализовать UUID в JSON.
     */
    public function testCase8(): void
    {
        //given
        $uuid = new Uuid('c5f808d2-3d80-4bc9-aff8-29703c9f1149');

        //when
        $result = json_encode($uuid);

        //then
        $this->assertEquals('"c5f808d2-3d80-4bc9-aff8-29703c9f1149"', $result);
    }
}
