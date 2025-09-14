<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\ValueObjects;

/**
 * Объект, который можно привести к числу с плавающей точкой
 */
interface FloatKindNumberInterface extends ComparableInterface
{
    /**
     * Получить десятичное представление типа.
     *
     * @return float Представление в виде числа в плавающей точкой
     */
    public function asFloat(): float;
}
