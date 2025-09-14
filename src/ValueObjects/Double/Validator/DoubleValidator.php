<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Double\Validator;

use Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions\DoubleIsTooBigException;
use Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions\DoubleIsTooSmallException;
use Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions\InvalidConstructionException;

/**
 * Валидатор типа Double
 */
readonly class DoubleValidator
{
    /**
     * @param string $typeName Название типа
     * @param float|null $min Минимально допустимое значение
     * @param float|null $max Максимально допустимое значение
     */
    public function __construct(
        protected string $typeName,
        protected ?float $min = null,
        protected ?float $max = null,
    ) {
        if (!is_null($this->min) && !is_null($this->max) && $this->min > $this->max) {
            throw new InvalidConstructionException(
                message: $typeName . ': Минимально допустимое значение не может быть больше максимального'
            );
        }
    }

    /**
     * Устанавливает не превышает ли проверяемое значение максимально допустимый порог.
     * Если максимальный порог имеет значение null валидация пропускается.
     *
     * @param float $double Проверяемое значение
     */
    public function thatValueIsNotTooBig(float $double): void
    {
        if (is_null($this->max)) {
            return;
        }

        if ($double > $this->max) {
            throw new DoubleIsTooBigException(message: $this->typeName . ' не может быть больше ' . $this->max);
        }
    }

    /**
     * Устанавливает не является ли проверяемое значение меньше минимально допустимого порога.
     * Если минимальный порог имеет значение null валидация пропускается.
     *
     * @param float $double Проверяемое значение
     */
    public function thatValueIsNotTooSmall(float $double): void
    {
        if (is_null($this->min)) {
            return;
        }

        if ($double < $this->min) {
            throw new DoubleIsTooSmallException(message: $this->typeName . ' не может быть меньше ' . $this->min);
        }
    }
}
