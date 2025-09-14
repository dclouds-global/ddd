<?php

declare(strict_types=1);

namespace Tests\ValueObjects\Amount;

use Dclouds\Ddd\Contracts\ValueObjects\ComparableInterface;
use Dclouds\Ddd\Contracts\ValueObjects\FloatKindNumberInterface;
use Dclouds\Ddd\ValueObjects\Amount\Amount;
use Dclouds\Ddd\ValueObjects\Amount\Exceptions\FailedAmountCreationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AmountTest extends TestCase
{
    /**
     * Тип создается с допустимым корректно
     */
    public function testCase1(): void
    {
        //expect
        $this->expectNotToPerformAssertions();

        //when
        new Amount(10);
    }

    /**
     * Если передаваемое значение меньше минимально допустимого
     * выбрасывается исключение
     */
    public function testCase2(): void
    {
        $this->expectException(FailedAmountCreationException::class);

        new class(amount: -5) extends Amount {
            protected ?int $min = 0;
        };
    }

    /**
     * Если передаваемое значение больше максимально допустимого
     * выбрасывается исключение
     */
    public function testCase3(): void
    {
        $this->expectException(FailedAmountCreationException::class);

        new class(amount: 5) extends Amount {
            protected ?int $max = 1;
        };
    }

    /**
     * Возвращает корректное скалярное целочисленное представление
     */
    public function testCase4(): void
    {
        //given
        $amount = new Amount(100);

        //then
        $this->assertEquals(100, $amount->asInteger());
    }

    /**
     * Возвращает корректное скалярное дробное представление
     */
    public function testCase5(): void
    {
        //given
        $amount = new Amount(125);

        //then
        $this->assertEquals(1.25, $amount->asFloat());
    }

    /**
     * Возвращает корректное строковое представление с форматированием
     */
    #[DataProvider('case6DataProvider')]
    public function testCase6(
        string $decimalSeparator,
        string $thousandsSeparator,
        int $value,
        string $expectedResult
    ): void {
        //given
        $amount = new Amount($value);

        //then
        $this->assertEquals($expectedResult, $amount->asString(
            decimalSeparator: $decimalSeparator,
            thousandsSeparator: $thousandsSeparator,
        ));
    }

    /**
     * @return array<array{decimalSeparator: string, thousandsSeparator: string, value: int, expectedResult: string}>
     */
    public static function case6DataProvider(): array
    {
        return [
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 134511,
                'expectedResult' => '1,345.11',
            ],
            [
                'decimalSeparator' => '',
                'thousandsSeparator' => '',
                'value' => 134511,
                'expectedResult' => '134511',
            ],
            [
                'decimalSeparator' => ',',
                'thousandsSeparator' => ' ',
                'value' => 134511,
                'expectedResult' => '1 345,11',
            ],
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 13,
                'expectedResult' => '0.13',
            ],
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 134,
                'expectedResult' => '1.34',
            ],
            [
                'decimalSeparator' => '.',
                'thousandsSeparator' => ',',
                'value' => 1345,
                'expectedResult' => '13.45',
            ],
        ];
    }

    /**
     * При прибавлении суммы возвращает новую сумму
     * с корректным значением
     */
    public function testCase7(): void
    {
        //given
        $baseAmount = new Amount(350);
        $additionalAmount = new Amount(150);

        //when
        $result = $baseAmount->add($additionalAmount);

        //then
        $this->assertEquals(500, $result->asInteger());
    }

    /**
     * При вычитании суммы возвращает новую сумму
     * с корректным значением
     */
    public function testCase8(): void
    {
        //given
        $baseAmount = new Amount(350);
        $subtractAmount = new Amount(150);

        //when
        $result = $baseAmount->subtract($subtractAmount);

        //then
        $this->assertEquals(200, $result->asInteger());
    }

    /**
     * При умножении на скаляр, либо тип, поддерживающий
     * значения с плавающей точкой, вернет новую сумму с корректным значением
     */
    #[DataProvider('case9DataProvider')]
    public function testCase9(float|FloatKindNumberInterface $multiplier): void
    {
        //given
        $baseAmount = new Amount(3);

        //when
        $result = $baseAmount->multiplyBy($multiplier);

        //then
        $this->assertEquals(7, $result->asInteger());
    }

    /**
     * @return array<string, array<string, int|Amount>>
     */
    public static function case9DataProvider(): array
    {
        return [
            'scalar' => ['multiplier' => 2.5],
            'type' => ['multiplier' => new Amount(250)],
        ];
    }

    /**
     * При делении на скаляр, либо тип, поддерживающий
     * значения с плавающей точкой, вернет новую сумму с корректным значением
     */
    #[DataProvider('case10DataProvider')]
    public function testCase10(float|FloatKindNumberInterface $divider): void
    {
        //given
        $baseAmount = new Amount(6);

        //when
        $result = $baseAmount->divisionBy($divider);

        //then
        $this->assertEquals(2, $result->asInteger());
    }

    /**
     * @return array<string, array<string, int|Amount>>
     */
    public static function case10DataProvider(): array
    {
        return [
            'scalar' => ['divider' => 2.5],
            'type' => ['divider' => new Amount(250)],
        ];
    }

    /**
     * Процент от суммы высчитывается корректно
     */
    #[DataProvider('case11DataProvider')]
    public function testCase11(float|FloatKindNumberInterface $percent): void
    {
        //given
        $baseAmount = new Amount(100);

        //when
        $result = $baseAmount->getPercent($percent);

        //then
        $this->assertEquals(2, $result->asInteger());
    }

    /**
     * @return array<string, array<string, float|Amount>>
     */
    public static function case11DataProvider(): array
    {
        return [
            'scalar' => ['percent' => 2.5],
            'type' => ['percent' => new Amount(250)],
        ];
    }

    /**
     * Сравнение суммы со скаляром, или другим числовым
     * сравнимым типом производится корректно.
     *
     * Сумма определяется как большее значение, если
     * передаваемое сравниваемое значение меньше.
     *
     * Сумма не определяется как большее значение, если
     * передаваемое сравниваемое значение больше или равно ей.
     */
    #[DataProvider('case12DataProvider')]
    public function testCase12(float|ComparableInterface $comparable, bool $expectedResult): void
    {
        //given
        $baseAmount = new Amount(250);

        //when
        $result = $baseAmount->isMoreThan($comparable);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<array{comparable: float|Amount, expectedResult: bool}>
     */
    public static function case12DataProvider(): array
    {
        return [
            ['comparable' => 1.25, 'expectedResult' => true],
            ['comparable' => new Amount(125), 'expectedResult' => true],
            ['comparable' => 2.5, 'expectedResult' => false],
            ['comparable' => new Amount(250), 'expectedResult' => false],
            ['comparable' => 3, 'expectedResult' => false],
            ['comparable' => new Amount(300), 'expectedResult' => false],
        ];
    }

    /**
     * Сравнение суммы со скаляром, или другим числовым
     * сравнимым типом производится корректно.
     *
     * Сумма определяется как меньшее значение, если
     * передаваемое сравниваемое значение больше.
     *
     * Сумма не определяется как большее значение, если
     * передаваемое сравниваемое значение меньше или равно ей.
     */
    #[DataProvider('case13DataProvider')]
    public function testCase13(float|ComparableInterface $comparable, bool $expectedResult): void
    {
        //given
        $baseAmount = new Amount(250);

        //when
        $result = $baseAmount->isLessThan($comparable);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<array{comparable: float|Amount, expectedResult: bool}>
     */
    public static function case13DataProvider(): array
    {
        return [
            ['comparable' => 3, 'expectedResult' => true],
            ['comparable' => new Amount(300), 'expectedResult' => true],
            ['comparable' => 2.5, 'expectedResult' => false],
            ['comparable' => new Amount(250), 'expectedResult' => false],
            ['comparable' => 1, 'expectedResult' => false],
            ['comparable' => new Amount(100), 'expectedResult' => false],
        ];
    }

    /**
     * Сравнение суммы со скаляром, или другим числовым
     * сравнимым типом производится корректно.
     *
     * Сумма определяется как равное значение, только в
     * том случае, когда передаваемое сравниваемое значение
     * равно ей.
     */
    #[DataProvider('case14DataProvider')]
    public function testCase14(float|ComparableInterface $comparable, bool $expectedResult): void
    {
        //given
        $baseAmount = new Amount(250);

        //when
        $result = $baseAmount->isEqual($comparable);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<array{comparable: float|Amount, expectedResult: bool}>
     */
    public static function case14DataProvider(): array
    {
        return [
            ['comparable' => 2.5, 'expectedResult' => true],
            ['comparable' => new Amount(250), 'expectedResult' => true],
            ['comparable' => 3, 'expectedResult' => false],
            ['comparable' => new Amount(300), 'expectedResult' => false],
            ['comparable' => 1, 'expectedResult' => false],
            ['comparable' => new Amount(100), 'expectedResult' => false],
        ];
    }

    /**
     * Можно получить доступ к корректному минимальному значению.
     */
    public function testCase15(): void
    {
        //given
        $baseAmount1 = new class(amount: 7) extends Amount {
            protected ?int $min = 5;
        };
        $baseAmount2 = new Amount(5);

        //then
        $this->assertEquals(5, $baseAmount1->getMinValue());
        $this->assertEquals(null, $baseAmount2->getMinValue());
    }

    /**
     * Можно получить доступ к корректному максимальному значению.
     */
    public function testCase16(): void
    {
        //given
        $baseAmount1 = new class(amount: 5) extends Amount {
            protected ?int $max = 7;
        };
        $baseAmount2 = new Amount(5);

        //then
        $this->assertEquals(7, $baseAmount1->getMaxValue());
        $this->assertEquals(null, $baseAmount2->getMaxValue());
    }

    /**
     * Можно сериализовать сумму в JSON.
     */
    public function testCase17(): void
    {
        //given
        $amount = new Amount(100);

        //when
        $result = json_encode($amount);

        //then
        $this->assertEquals('"1,00"', $result);
    }
}
