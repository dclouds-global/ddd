<?php

declare(strict_types=1);

namespace Tests\ValueObjects\Double;

use Dclouds\Ddd\Contracts\ValueObjects\ComparableInterface;
use Dclouds\Ddd\Contracts\ValueObjects\FloatKindNumberInterface;
use Dclouds\Ddd\ValueObjects\Double\Double;
use Dclouds\Ddd\ValueObjects\Double\Exceptions\FailedDoubleCreationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DoubleTest extends TestCase
{
    /**
     * Тип создается с допустимым корректно
     */
    public function testCase1(): void
    {
        //expect
        $this->expectNotToPerformAssertions();

        //when
        new Double(10.7);
    }

    /**
     * Если передаваемое значение меньше минимально допустимого
     * выбрасывается исключение
     */
    public function testCase2(): void
    {
        $this->expectException(FailedDoubleCreationException::class);

        new class(double: -5.3) extends Double {
            protected ?float $min = 0;
        };
    }

    /**
     * Если передаваемое значение больше максимально допустимого
     * выбрасывается исключение
     */
    public function testCase3(): void
    {
        $this->expectException(FailedDoubleCreationException::class);

        new class(double: 5.5) extends Double {
            protected ?float $max = 1;
        };
    }

    /**
     * Возвращает корректное скалярное дробное представление
     */
    public function testCase4(): void
    {
        //given
        $double = new Double(10.3);

        //then
        $this->assertEquals(10.3, $double->asFloat());
    }

    /**
     * Возвращает корректное строковое представление с форматированием
     */
    #[DataProvider('case5DataProvider')]
    public function testCase5(
        string $decimalSeparator,
        string $thousandsSeparator,
        float $value,
        string $expectedResult
    ): void {
        //given
        $amount = new Double($value);

        //then
        $this->assertEquals($expectedResult, $amount->asString(
            decimalSeparator: $decimalSeparator,
            thousandsSeparator: $thousandsSeparator,
        ));
    }

    /**
     * @return array<array{decimalSeparator: string, thousandsSeparator: string, value: float, expectedResult: string}>
     */
    public static function case5DataProvider(): array
    {
        return [
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 1345.11,
                'expectedResult' => '1,345.11',
            ],
            [
                'decimalSeparator' => '',
                'thousandsSeparator' => '',
                'value' => 1345.11,
                'expectedResult' => '134511',
            ],
            [
                'decimalSeparator' => ',',
                'thousandsSeparator' => ' ',
                'value' => 1345.11,
                'expectedResult' => '1 345,11',
            ],
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => .13,
                'expectedResult' => '0.13',
            ],
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 1.34,
                'expectedResult' => '1.34',
            ],
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 13.45,
                'expectedResult' => '13.45',
            ],
        ];
    }

    /**
     * При прибавлении значения возвращает новую корректную сумму
     */
    public function testCase6(): void
    {
        //given
        $baseDouble = new Double(3.5);
        $additionalDouble = new Double(1.51);

        //when
        $result1 = $baseDouble->add($additionalDouble);
        $result2 = $baseDouble->add(1.52);

        //then
        $this->assertEquals(5.01, $result1->asFloat());
        $this->assertEquals(5.02, $result2->asFloat());
    }

    /**
     * При вычитании значения возвращает новую корректную разницу
     */
    public function testCase7(): void
    {
        //given
        $baseDouble = new Double(3.5);
        $subtractDouble = new Double(1.5);

        //when
        $result1 = $baseDouble->subtract($subtractDouble);
        $result2 = $baseDouble->subtract(1.51);

        //then
        $this->assertEquals(2, $result1->asFloat());
        $this->assertEquals(1.99, $result2->asFloat());
    }

    /**
     * При умножении на скаляр, либо тип, поддерживающий
     * значения с плавающей точкой, вернет новое произведение
     * с корректным значением
     */
    #[DataProvider('case8DataProvider')]
    public function testCase8(float|FloatKindNumberInterface $multiplier): void
    {
        //given
        $baseDouble = new Double(3);

        //when
        $result = $baseDouble->multiplyBy($multiplier);

        //then
        $this->assertEquals(7.5, $result->asFloat());
    }

    /**
     * @return array<string, array<string, float|Double>>
     */
    public static function case8DataProvider(): array
    {
        return [
            'scalar' => ['multiplier' => 2.5],
            'type' => ['multiplier' => new Double(2.5)],
        ];
    }

    /**
     * При делении на скаляр, либо тип, поддерживающий
     * значения с плавающей точкой, вернет новое частное
     * с корректным значением
     */
    #[DataProvider('case9DataProvider')]
    public function testCase9(float|FloatKindNumberInterface $divider): void
    {
        //given
        $baseDouble = new Double(6);

        //when
        $result = $baseDouble->divisionBy($divider);

        //then
        $this->assertEquals(2.4, $result->asFloat());
    }

    /**
     * @return array<string, array<string, float|Double>>
     */
    public static function case9DataProvider(): array
    {
        return [
            'scalar' => ['divider' => 2.5],
            'type' => ['divider' => new Double(2.5)],
        ];
    }

    /**
     * Процент от значения высчитывается корректно
     */
    #[DataProvider('case10DataProvider')]
    public function testCase10(float|FloatKindNumberInterface $percent): void
    {
        //given
        $baseDouble = new Double(10);

        //when
        $result = $baseDouble->getPercent($percent);

        //then
        $this->assertEquals(.25, $result->asFloat());
    }

    /**
     * @return array<string, array<string, float|Double>>
     */
    public static function case10DataProvider(): array
    {
        return [
            'scalar' => ['percent' => 2.5],
            'type' => ['percent' => new Double(2.5)],
        ];
    }

    /**
     * Сравнение типа со скаляром, или другим числовым
     * сравнимым типом производится корректно.
     *
     * Значение типа определяется как большее, если
     * передаваемое сравниваемое значение меньше.
     *
     * Значение типа не определяется как большее, если
     * передаваемое сравниваемое значение больше или равно ему.
     */
    #[DataProvider('case11DataProvider')]
    public function testCase11(float|ComparableInterface $comparable, bool $expectedResult): void
    {
        //given
        $baseDouble = new Double(2.5);

        //when
        $result = $baseDouble->isMoreThan($comparable);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<array{comparable: float|Double, expectedResult: bool}>
     */
    public static function case11DataProvider(): array
    {
        return [
            ['comparable' => 1.25, 'expectedResult' => true],
            ['comparable' => new Double(1.25), 'expectedResult' => true],
            ['comparable' => 2.5, 'expectedResult' => false],
            ['comparable' => new Double(2.5), 'expectedResult' => false],
            ['comparable' => 3, 'expectedResult' => false],
            ['comparable' => new Double(3), 'expectedResult' => false],
        ];
    }

    /**
     * Сравнение типа со скаляром, или другим числовым
     * сравнимым типом производится корректно.
     *
     * Значение типа определяется как меньшее, если
     * передаваемое сравниваемое значение больше.
     *
     * Значение типа не определяется как большее, если
     * передаваемое сравниваемое значение меньше или равно ему.
     */
    #[DataProvider('case12DataProvider')]
    public function testCase12(float|ComparableInterface $comparable, bool $expectedResult): void
    {
        //given
        $baseDouble = new Double(2.5);

        //when
        $result = $baseDouble->isLessThan($comparable);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<array{comparable: float|Double, expectedResult: bool}>
     */
    public static function case12DataProvider(): array
    {
        return [
            ['comparable' => 3, 'expectedResult' => true],
            ['comparable' => new Double(3), 'expectedResult' => true],
            ['comparable' => 2.5, 'expectedResult' => false],
            ['comparable' => new Double(2.5), 'expectedResult' => false],
            ['comparable' => 1, 'expectedResult' => false],
            ['comparable' => new Double(1), 'expectedResult' => false],
        ];
    }

    /**
     * Сравнение типа со скаляром, или другим числовым
     * сравнимым типом производится корректно.
     *
     * Значение типа определяется как равное, только в
     * том случае, когда передаваемое сравниваемое значение
     * равно ему.
     */
    #[DataProvider('case13DataProvider')]
    public function testCase13(float|ComparableInterface $comparable, bool $expectedResult): void
    {
        //given
        $baseDouble = new Double(2.5);

        //when
        $result = $baseDouble->isEqual($comparable);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<array{comparable: float|Double, expectedResult: bool}>
     */
    public static function case13DataProvider(): array
    {
        return [
            ['comparable' => 2.5, 'expectedResult' => true],
            ['comparable' => new Double(2.5), 'expectedResult' => true],
            ['comparable' => 3, 'expectedResult' => false],
            ['comparable' => new Double(3), 'expectedResult' => false],
            ['comparable' => 1, 'expectedResult' => false],
            ['comparable' => new Double(1), 'expectedResult' => false],
        ];
    }

    /**
     * Можно получить доступ к корректному минимальному значению.
     */
    public function testCase14(): void
    {
        //given
        $baseDouble1 = new class(double: 7.3) extends Double {
            protected ?float $min = 5.5;
        };
        $baseDouble2 = new Double(5);

        //then
        $this->assertEquals(5.5, $baseDouble1->getMinValue());
        $this->assertEquals(null, $baseDouble2->getMinValue());
    }

    /**
     * Можно получить доступ к корректному максимальному значению.
     */
    public function testCase15(): void
    {
        //given
        $baseDouble1 = new class(double: 5.5) extends Double {
            protected ?float $max = 7.3;
        };
        $baseDouble2 = new Double(5);

        //then
        $this->assertEquals(7.3, $baseDouble1->getMaxValue());
        $this->assertEquals(null, $baseDouble2->getMaxValue());
    }

    /**
     * Можно сериализовать число с плавающей точкой в JSON.
     */
    public function testCase16(): void
    {
        //given
        $double = new Double(100.5);

        //when
        $result = json_encode($double);

        //then
        $this->assertEquals(100.5, $result);
    }
}
