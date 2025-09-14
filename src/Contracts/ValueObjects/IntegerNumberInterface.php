<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\ValueObjects;

/**
 * Объект, который можно привести к целому числу
 */
interface IntegerNumberInterface
{
    /**
     * Получить целочисленное скалярное представление типа.
     *
     * @return int Целочисленное представление
     */
    public function asInteger(): int;
}
