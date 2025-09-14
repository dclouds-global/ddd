<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\ValueObjects;

/**
 * Объект представляющий денежную сумму
 */
interface MoneyInterface extends IntegerNumberInterface, FloatKindNumberInterface, ComparableInterface
{
}
