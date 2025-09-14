<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Paginators;

/**
 * Класс реализации пагинации
 *
 * Содержит экземпляры сущностей и информацию для пагинации
 *
 * @template T
 */
abstract readonly class Paginator
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        private(set) array $items,
        private(set) PageInfo $pageInfo,
    ) {
    }
}
