<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\ValueObjects;

/**
 * Тип сравнимый с числом с плавающей точкой
 */
interface ComparableInterface
{
    /**
     * Проверка, является ли значение текущего типа больше
     * передаваемого значения
     *
     * @param float|ComparableInterface $other Сравниваемое значение
     * @return bool Результат сравнения
     */
    public function isMoreThan(float|ComparableInterface $other): bool;

    /**
     * Проверка, является ли значение текущего типа меньше
     * передаваемого значения
     *
     * @param float|ComparableInterface $other Сравниваемое значение
     * @return bool Результат сравнения
     */
    public function isLessThan(float|ComparableInterface $other): bool;

    /**
     * Проверка, является ли значение текущего типа равным
     * передаваемому значению
     *
     * @param float|ComparableInterface $other Сравниваемое значение
     * @return bool Результат сравнения
     */
    public function isEqual(float|ComparableInterface $other): bool;
}
