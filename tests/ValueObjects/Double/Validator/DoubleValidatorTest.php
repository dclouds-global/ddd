<?php

declare(strict_types=1);

namespace Tests\ValueObjects\Double\Validator;

use Dclouds\Ddd\ValueObjects\Double\Validator\DoubleValidator;
use Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions\DoubleIsTooBigException;
use Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions\DoubleIsTooSmallException;
use Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions\InvalidConstructionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DoubleValidator::class)]
final class DoubleValidatorTest extends TestCase
{
    /**
     * При валидации минимального и максимального значений
     * исключение не выбрасывается при условии, что эти значения
     * укладываются в указанный в конструкторе типа диапазон.
     *
     * @param null|float $min Минимально допустимое значение типа
     * @param null|float $max Максимально допустимое значение типа
     * @param float $value Валидируемое значение
     */
    #[DataProvider('case1DataProvider')]
    public function testCase1(?float $min, ?float $max, float $value): void
    {
        //given
        $validator = new DoubleValidator(
            typeName: 'typeName',
            min: $min,
            max: $max
        );

        //expect
        $this->expectNotToPerformAssertions();

        //when
        $validator->thatValueIsNotTooSmall($value);
        $validator->thatValueIsNotTooSmall($value);
    }

    /**
     * @return array<string, array<string, float|null>>
     */
    public static function case1DataProvider(): array
    {
        return [
            'null min, null max, negative value' => ['min' => null, 'max' => null, 'value' => -1.1],
            'null min, null max, zero value' => ['min' => null, 'max' => null, 'value' => 0],
            'null min, null max, positive value' => ['min' => null, 'max' => null, 'value' => 0.5],
            'null min, negative max, negative value' => ['min' => null, 'max' => -1.5, 'value' => -2.5],
            'null min, positive max, positive value' => ['min' => null, 'max' => 2.5, 'value' => 1.5],
            'null min, positive max, zero value' => ['min' => null, 'max' => 2.5, 'value' => 0],
            'negative min, null max, negative value' => ['min' => -1.5, 'max' => null, 'value' => -1.5],
            'negative min, null max, zero value' => ['min' => -1.7, 'max' => null, 'value' => 0],
            'negative min, null max, positive value' => ['min' => -1.7, 'max' => null, 'value' => 1.7],
            'negative min, negative max, negative value' => ['min' => -2.3, 'max' => -1.4, 'value' => -1.4],
            'negative min, zero max, negative value' => ['min' => -1.7, 'max' => 0, 'value' => -1.7],
            'negative min, zero max, zero value' => ['min' => -1.5, 'max' => 0, 'value' => 0],
            'negative min, positive max, negative value' => ['min' => -1.5, 'max' => 1.5, 'value' => -1.5],
            'negative min, positive max, zero value' => ['min' => -1.5, 'max' => 1.5, 'value' => 0],
            'negative min, positive max, positive value' => ['min' => -1.5, 'max' => 1.5, 'value' => 1],
            'positive min, null max, positive value' => ['min' => 1.5, 'max' => null, 'value' => 1.5],
            'positive min, positive max, positive value' => ['min' => 1.5, 'max' => 1.5, 'value' => 1.5],
        ];
    }

    /**
     * Выбрасывает исключение, если валидируемое значение
     * меньше минимально допустимого
     *
     * @property float $min Минимально допустимое значение
     * @property float $value Валидируемое значение
     */
    #[DataProvider('case2DataProvider')]
    public function testCase2(float $min, float $value): void
    {
        //given
        $validator = new DoubleValidator(
            typeName: 'typeName',
            min: $min,
            max: null,
        );

        //expect
        $this->expectException(DoubleIsTooSmallException::class);

        //when
        $validator->thatValueIsNotTooSmall($value);
    }

    /**
     * @return array<array<string, float>>
     */
    public static function case2DataProvider(): array
    {
        return [
            ['min' => -2.5, 'value' => -3.5],
            ['min' => 0.5, 'value' => -2.5],
            ['min' => 1.5, 'value' => -1.5],
        ];
    }

    /**
     * Выбрасывает исключение, если валидируемое значение
     * больше максимально допустимого
     *
     * @property float $max Максимально допустимое значение
     * @property float $value Валидируемое значение
     */
    #[DataProvider('case3DataProvider')]
    public function testCase3(float $max, float $value): void
    {
        //given
        $validator = new DoubleValidator(
            typeName: 'typeName',
            min: null,
            max: $max,
        );

        //expect
        $this->expectException(DoubleIsTooBigException::class);

        //when
        $validator->thatValueIsNotTooBig($value);
    }

    /**
     * @return array<array<string, float>>
     */
    public static function case3DataProvider(): array
    {
        return [
            ['max' => -2.5, 'value' => -1.5],
            ['max' => 0.5, 'value' => 1.5],
            ['max' => 2.5, 'value' => 3.5],
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
        new DoubleValidator(
            typeName: 'typeName',
            min: 2,
            max: 1
        );
    }
}
