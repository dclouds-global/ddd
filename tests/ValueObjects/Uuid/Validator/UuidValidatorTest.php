<?php

declare(strict_types=1);

namespace Tests\ValueObjects\Uuid\Validator;

use Dclouds\Ddd\ValueObjects\Uuid\Validator\Exceptions\InvalidUUIDFormatException;
use Dclouds\Ddd\ValueObjects\Uuid\Validator\UuidValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UuidValidator::class)]
final class UuidValidatorTest extends TestCase
{
    private UuidValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new UuidValidator('typeName');
    }

    /**
     * При валидации строки на соответствие формату UUID не выбрасывается
     * исключение, если передаваемая строка имеет указанный формат
     */
    public function testCase1(): void
    {
        //expect
        $this->expectNotToPerformAssertions();

        //when
        $this->validator->thatIdIsValidUUIDv4String('c5f808d2-3d80-4bc9-aff8-29703c9f1149');
    }

    /**
     * При валидации строки на соответствие формату UUID, если передаваемая
     * строка имеет формат отличный от UUIDv4 выбрасывается исключение
     */
    #[DataProvider('case2DataProvider')]
    public function testCase2(string $value): void
    {
        //expect
        $this->expectException(InvalidUUIDFormatException::class);

        //when
        $this->validator->thatIdIsValidUUIDv4String($value);
    }

    /**
     * @return array<string, array<string>>
     */
    public static function case2DataProvider(): array
    {
        return [
            'UUIDv1' => ['513422ac-938b-11ef-b864-0242ac120002'],
            'UUIDv7' => ['0192c888-cc7b-7b28-852b-36ac17279ebb'],
            'Wrong version bit' => ['c5f808d2-3d80-3bc9-aff8-29703c9f1149'],
            'Wrong variant' => ['c5f808d2-3d80-4bc9-cff8-29703c9f1149'],
            'Raw String' => ['abc'],
            'Empty String' => ['abc'],
        ];
    }
}
