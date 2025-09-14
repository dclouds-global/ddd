<?php

declare(strict_types=1);

namespace Tests\Paginators;

use Dclouds\Ddd\Paginators\PageInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageInfoTest::class)]
final class PageInfoTest extends TestCase
{
    /**
     * Проверяем корректные расчеты параметров пагинации:
     * * totalPages
     * * hasNextPage
     * * hasPreviousPage
     *
     * @param int $totalItems количество сущностей
     * @param int $page номер текущей страницы
     * @param int $perPage количество сущностей на странице
     * @param int $expectedTotalPages ожидаемое расчетное количество страниц
     * @param bool $expectedHasNextPage ожидаемое расчетное наличие следующей страницы
     * @param bool $expectedHasPreviousPage ожидаемое расчетное наличие предыдущей страницы
     */
    #[DataProvider('case1DataProvider')]
    public function testCase1(int $totalItems, int $page, int $perPage, int $expectedTotalPages, bool $expectedHasNextPage, bool $expectedHasPreviousPage): void
    {
        //when
        $pageInfo = new PageInfo(
            totalItems: $totalItems,
            page: $page,
            perPage: $perPage,
        );

        //expect
        $this->assertEquals($expectedTotalPages, $pageInfo->totalPages);
        $this->assertEquals($expectedHasNextPage, $pageInfo->hasNextPage);
        $this->assertEquals($expectedHasPreviousPage, $pageInfo->hasPreviousPage);
    }

    /**
     * Проверяем выкидывание exceptions
     * * Количество сущностей выходит за диапазон
     * * Текущая страница выходит за диапазон
     * * Количество на странице выходит за диапазон
     *
     * @param int $totalItems количество сущностей
     * @param int $page номер текущей страницы
     * @param int $perPage количество сущностей на странице
     * @param string $expectedExceptionMessage ожидаемый текст исключения
     */
    #[DataProvider('case2DataProvider')]
    public function testCase2(int $totalItems, int $page, int $perPage, string $expectedExceptionMessage): void
    {
        //expect
        $this->expectExceptionMessage($expectedExceptionMessage);

        //when
        new PageInfo(
            totalItems: $totalItems,
            page: $page,
            perPage: $perPage,
        );
    }

    /**
     * @return array<array<string, int>>
     */
    public static function case1DataProvider(): array
    {
        return [
            ['totalItems' => 1, 'page' => 1, 'perPage' => 1, 'expectedTotalPages' => 1, 'expectedHasNextPage' => false, 'expectedHasPreviousPage' => false],
            ['totalItems' => 2, 'page' => 1, 'perPage' => 1, 'expectedTotalPages' => 2, 'expectedHasNextPage' => true, 'expectedHasPreviousPage' => false],
            ['totalItems' => 2, 'page' => 2, 'perPage' => 1, 'expectedTotalPages' => 2, 'expectedHasNextPage' => false, 'expectedHasPreviousPage' => true],
            ['totalItems' => 0, 'page' => 1, 'perPage' => 1, 'expectedTotalPages' => 0, 'expectedHasNextPage' => false, 'expectedHasPreviousPage' => false],
            ['totalItems' => 25, 'page' => 3, 'perPage' => 3, 'expectedTotalPages' => 9, 'expectedHasNextPage' => true, 'expectedHasPreviousPage' => true],
        ];
    }

    /**
     * @return array<array<string, int>>
     */
    public static function case2DataProvider(): array
    {
        return [
            ['totalItems' => -1, 'page' => 1, 'perPage' => 1, 'expectedExceptionMessage' => 'Количество сущностей выходит за диапазон'],
            ['totalItems' => 1, 'page' => 0, 'perPage' => 1, 'expectedExceptionMessage' => 'Текущая страница выходит за диапазон'],
            ['totalItems' => 1, 'page' => -1, 'perPage' => 1, 'expectedExceptionMessage' => 'Текущая страница выходит за диапазон'],
            ['totalItems' => 1, 'page' => 1, 'perPage' => 0, 'expectedExceptionMessage' => 'Количество на странице выходит за диапазон'],
            ['totalItems' => 1, 'page' => 1, 'perPage' => -1, 'expectedExceptionMessage' => 'Количество на странице выходит за диапазон'],
        ];
    }
}
