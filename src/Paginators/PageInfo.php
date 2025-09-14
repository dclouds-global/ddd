<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Paginators;

use Dclouds\Ddd\Paginators\Exceptions\RangeException;

/**
 * Класс с информацией о пагинации
 *
 * @property-read int $totalItems количество сущностей
 * @property-read int $page страница
 * @property-read int $perPage количество на странице
 */
final readonly class PageInfo
{
    /**
     * Существует ли следующая страница
     */
    private(set) bool $hasNextPage;

    /**
     * Существует ли предыдущая страница
     */
    private(set) bool $hasPreviousPage;

    /**
     * Количество страниц
     *
     * @property-read int $totalPages количество страниц
     */
    private(set) int $totalPages;

    /**
     * Создание объекта с информацией пагинации
     *
     * Проверки:
     * * Количество сущностей не может быть меньше 0
     * * Текущая страница не может быть меньше 1
     * * Количество на странице не может быть меньше 1
     *
     * Рассчитывается количество страниц, а не передается, так как проверка валидности требует расчета параметра
     *
     * Не проверяется вхождение параметра $page в диапазон количества страниц. Для работы ситуаций при 0 сущностей и 1 странице
     *
     * @param int $totalItems
     * @param int $page
     * @param int $perPage
     */
    public function __construct(
        private(set) int $totalItems,
        private(set) int $page,
        private(set) int $perPage,
    ) {
        if ($this->totalItems <= -1) {
            throw new RangeException(message: 'Количество сущностей выходит за диапазон');
        }

        if ($this->page < 1) {
            throw new RangeException(message: 'Текущая страница выходит за диапазон');
        }

        if ($this->perPage < 1) {
            throw new RangeException(message: 'Количество на странице выходит за диапазон');
        }

        $this->totalPages = (int) ceil($this->totalItems / $this->perPage);

        $this->hasNextPage = $page < $this->totalPages;
        $this->hasPreviousPage = $page > 1;
    }
}
