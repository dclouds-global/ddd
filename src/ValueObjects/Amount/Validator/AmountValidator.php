<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Amount\Validator;

use Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions\AmountIsTooBigException;
use Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions\AmountIsTooSmallException;
use Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions\InvalidConstructionException;

/**
 * Валидатор типа Amount.
 *
 * Базируется на целочисленном типе данных, состоящем из младших валютных единиц (копеек, центов и тд)
 */
readonly class AmountValidator
{
    /**
     * @param string $typeName Название типа
     * @param int|null $min Минимально допустимое значение
     * @param int|null $max Максимально допустимое значение
     */
    public function __construct(
        protected string $typeName,
        protected ?int $min = null,
        protected ?int $max = null,
    )
    {
        if (!is_null($this->min) && !is_null($this->max) && $this->min > $this->max) {
            throw new InvalidConstructionException(
                message: $this->typeName . ': Минимально допустимое значение не может быть больше максимального'
            );
        }
    }

    /**
     * Устанавливает не превышает ли проверяемое значение максимально допустимый порог.
     * Если максимальный порог имеет значение null валидация пропускается.
     *
     * @param int $amount Проверяемое значение
     */
    public function thatAmountIsNotTooBig(int $amount): void
    {
        if (is_null($this->max)) {
            return;
        }

        if ($amount > $this->max) {
            throw new AmountIsTooBigException(message: $this->typeName. ' не может быть больше ' . $this->max);
        }
    }

    /**
     * Устанавливает не является ли проверяемое значение меньше минимально допустимого порога.
     * Если минимальный порог имеет значение null валидация пропускается.
     *
     * @param int $amount Проверяемое значение
     */
    public function thatAmountIsNotTooSmall(int $amount): void
    {
        if (is_null($this->min)) {
            return;
        }

        if ($amount < $this->min) {
            throw new AmountIsTooSmallException(message: $this->typeName. ' не может быть меньше ' . $this->min);
        }
    }
}
