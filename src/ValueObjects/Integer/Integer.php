<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Integer;

use Dclouds\Ddd\ValueObjects\Integer\Exceptions\IntegerIsTooHighException;
use Dclouds\Ddd\ValueObjects\Integer\Exceptions\IntegerIsTooLowException;
use Dclouds\Ddd\ValueObjects\Integer\Exceptions\InvalidIntegerConfigurationException;
use JsonSerializable;

/**
 * Целое число
 */
class Integer implements JsonSerializable
{
    /**
     * Название типа
     */
    protected string $typeName = 'Целое число';

    /**
     * Минимально допустимое значение
     */
    protected ?int $min = null;

    /**
     * Максимально допустимое значение
     */
    protected ?int $max = null;

    public function __construct(protected int $int)
    {
        if (!is_null($this->min) && !is_null($this->max) && $this->min >= $this->max) {
            throw new InvalidIntegerConfigurationException(
                message: 'Минимальное значение типа ' . $this->typeName . ' не может превышать максимальное'
            );
        }

        if (!is_null($this->min) && $int < $this->min) {
            throw new IntegerIsTooLowException(message: $this->typeName . ' не может быть меньше' . $this->min);
        }

        if (!is_null($this->max) && $int > $this->max) {
            throw new IntegerIsTooHighException(message: $this->typeName . ' не может быть больше' . $this->max);
        }
    }

    public function asInteger(): int
    {
        return $this->int;
    }

    public function add(int|self $value): static
    {
        if (is_object($value)) {
            return new static($this->int + $value->asInteger());
        }

        return new static($this->int + $value);
    }

    public function subtract(int|self $value): static
    {
        if (is_object($value)) {
            return new static($this->int - $value->asInteger());
        }

        return new static($this->int - $value);
    }

    public function jsonSerialize(): int
    {
        return $this->asInteger();
    }
}
