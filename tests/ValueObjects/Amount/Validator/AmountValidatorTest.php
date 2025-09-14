<?php

declare(strict_types=1);

namespace Tests\ValueObjects\Amount\Validator;

use Dclouds\Ddd\ValueObjects\Amount\Validator\AmountValidator;
use Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions\AmountIsTooBigException;
use Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions\AmountIsTooSmallException;
use Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions\InvalidConstructionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AmountValidator::class)]
final class AmountValidatorTest extends TestCase
{
    /**
     * При валидации минимального и максимального значений
     * исключение не выбрасывается при условии, что эти значения
     * укладываются в указанный в конструкторе типа диапазон.
     *
     * @param int|null $min Минимально допустимое значение типа
     * @param int|null $max Максимально допустимое значение типа
     * @param int $value Валидируемое значение
     */
    #[DataProvider('case1DataProvider')]
    public function testCase1(?int $min, ?int $max, int $value): void
    {
        //given
        $validator = new AmountValidator(
            typeName: 'typeName',
            min: $min,
            max: $max
        );

        //expect
        $this->expectNotToPerformAssertions();

        //when
        $validator->thatAmountIsNotTooSmall($value);
        $validator->thatAmountIsNotTooBig($value);
    }

    /**
     * @return array<string, array<string, int|null>>
     */
    public static function case1DataProvider(): array
    {
        return [
            'null min, null max, negative value' => ['min' => null, 'max' => null, 'value' => -1],
            'null min, null max, zero value' => ['min' => null, 'max' => null, 'value' => 0],
            'null min, null max, positive value' => ['min' => null, 'max' => null, 'value' => 1],
            'null min, negative max, negative value' => ['min' => null, 'max' => -1, 'value' => -2],
            'null min, positive max, positive value' => ['min' => null, 'max' => 2, 'value' => 1],
            'null min, positive max, zero value' => ['min' => null, 'max' => 2, 'value' => 0],
            'negative min, null max, negative value' => ['min' => -1, 'max' => null, 'value' => -1],
            'negative min, null max, zero value' => ['min' => -1, 'max' => null, 'value' => 0],
            'negative min, null max, positive value' => ['min' => -1, 'max' => null, 'value' => 1],
            'negative min, negative max, negative value' => ['min' => -2, 'max' => -1, 'value' => -1],
            'negative min, zero max, negative value' => ['min' => -1, 'max' => 0, 'value' => -1],
            'negative min, zero max, zero value' => ['min' => -1, 'max' => 0, 'value' => 0],
            'negative min, positive max, negative value' => ['min' => -1, 'max' => 1, 'value' => -1],
            'negative min, positive max, zero value' => ['min' => -1, 'max' => 1, 'value' => 0],
            'negative min, positive max, positive value' => ['min' => -1, 'max' => 1, 'value' => 1],
            'positive min, null max, positive value' => ['min' => 1, 'max' => null, 'value' => 1],
            'positive min, positive max, positive value' => ['min' => 1, 'max' => 1, 'value' => 1],
        ];
    }

    /**
     * Выбрасывает исключение, если валидируемое значение
     * меньше минимально допустимого
     *
     * @property int $min Минимально допустимое значение
     * @property int $value Валидируемое значение
     */
    #[DataProvider('case2DataProvider')]
    public function testCase2(int $min, int $value): void
    {
        //given
        $validator = new AmountValidator(
            typeName: 'typeName',
            min: $min,
            max: null,
        );

        //expect
        $this->expectException(AmountIsTooSmallException::class);

        //when
        $validator->thatAmountIsNotTooSmall($value);
    }

    /**
     * @return array<array<string, int>>
     */
    public static function case2DataProvider(): array
    {
        return [
            ['min' => -2, 'value' => -3],
            ['min' => 0, 'value' => -2],
            ['min' => 1, 'value' => -1],
        ];
    }

    /**
     * Выбрасывает исключение, если валидируемое значение
     * больше максимально допустимого
     *
     * @property int $max Максимально допустимое значение
     * @property int $value Валидируемое значение
     */
    #[DataProvider('case3DataProvider')]
    public function testCase3(int $max, int $value): void
    {
        //given
        $validator = new AmountValidator(
            typeName: 'typeName',
            min: null,
            max: $max,
        );

        //expect
        $this->expectException(AmountIsTooBigException::class);

        //when
        $validator->thatAmountIsNotTooBig($value);
    }

    /**
     * @return array<array<string, int>>
     */
    public static function case3DataProvider(): array
    {
        return [
            ['max' => -2, 'value' => -1],
            ['max' => 0, 'value' => 1],
            ['max' => 2, 'value' => 3],
        ];
    }

    /**
     * Если при создании валидатора минимально допустимое значение больше
     * максимально допустимого - будет выброшено исключение
     */
    public function testCase4(): void
    {
        //expect
        $this->expectException(InvalidConstructionException::class);

        //when
        new AmountValidator(
            typeName: 'typeName',
            min: 2,
            max: 1
        );
    }
}
