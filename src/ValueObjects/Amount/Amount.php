<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Amount;

use Dclouds\Ddd\Contracts\ValueObjects\ComparableInterface;
use Dclouds\Ddd\Contracts\ValueObjects\FloatKindNumberInterface;
use Dclouds\Ddd\Contracts\ValueObjects\MoneyInterface;
use Dclouds\Ddd\ValueObjects\Amount\Exceptions\FailedAmountCreationException;
use Dclouds\Ddd\ValueObjects\Amount\Validator\AmountValidator;
use DomainException;
use JsonSerializable;

/**
 * Денежная сумма
 */
class Amount implements MoneyInterface, JsonSerializable
{
    /**
     * Название типа
     */
    protected string $typeName = 'Сумма';

    /**
     * Минимально допустимое значение.
     *
     * Если задано null, минимальное значение отсутствует.
     */
    protected ?int $min = null;

    /**
     * Максимально допустимое значение.
     *
     * Если задано null, максимальное значение отсутствует.
     */
    protected ?int $max = null;

    /**
     * Валидатор значения
     */
    protected AmountValidator $validator;

    /**
     * Создание нового экземпляра типа Amount
     *
     * @param int $amount целочисленное представление сумма
     */
    public function __construct(protected readonly int $amount)
    {
        $this->validator = new AmountValidator(
            typeName: $this->typeName,
            min: $this->min,
            max: $this->max,
        );
        $this->validate($this->amount);
    }

    /**
     * Получить целочисленное скалярное представление типа.
     * Указывается в младшем разряде валюты (копейках, центах и тд)
     *
     * @return int Целочисленное представление суммы
     */
    public function asInteger(): int
    {
        return $this->amount;
    }

    /**
     * Получить десятичное представление типа
     *
     * @return float Представление в виде числа в плавающей точкой
     */
    public function asFloat(): float
    {
        return $this->amount / 100;
    }

    /**
     * Получить строковое форматированное представление типа.
     *
     * @param string $decimalSeparator Разделитель десятичной части
     * @param string $thousandsSeparator Разделитель разряда
     * @return string Строковое представление
     */
    public function asString(string $decimalSeparator = ',', string $thousandsSeparator = ' '): string
    {
        return number_format(
            num: $this->asFloat(),
            decimals: 2,
            decimal_separator: $decimalSeparator,
            thousands_separator: $thousandsSeparator
        );
    }

    /**
     * Проверка, является ли значение текущего типа больше
     * передаваемого значения.
     *
     * При сравнении приводятся к типу float.
     *
     * @param float|ComparableInterface $other Сравниваемое значение
     * @return bool Результат сравнения
     */
    public function isMoreThan(float|ComparableInterface $other): bool
    {
        if (is_float($other)) {
            return $this->amount / 100 > $other;
        }

        return $other->isLessThan($this->amount / 100);
    }

    /**
     * Проверка, является ли значение текущего типа меньше
     * передаваемого значения.
     *
     * При сравнении приводятся к типу float.
     *
     * @param float|ComparableInterface $other Сравниваемое значение
     * @return bool Результат сравнения
     */
    public function isLessThan(float|ComparableInterface $other): bool
    {
        if (is_float($other)) {
            return $this->amount / 100 < $other;
        }

        return $other->isMoreThan($this->amount / 100);
    }

    /**
     * Проверка, является ли значение текущего типа равным
     * передаваемому значению.
     *
     * При сравнении приводятся к типу float.
     *
     * @param float|ComparableInterface $other Сравниваемое значение
     * @return bool Результат сравнения
     */
    public function isEqual(float|ComparableInterface $other): bool
    {
        if (is_float($other)) {
            return $this->amount / 100 === $other;
        }

        return $other->isEqual($this->amount / 100);
    }

    /**
     * Добавить сумму к текущей сумме.
     *
     * @param MoneyInterface $additionalAmount Добавляемая сумма
     * @return static Новый экземпляр типа с итоговой суммой
     */
    public function add(MoneyInterface $additionalAmount): static
    {
        $amount = $this->amount + $additionalAmount->asInteger();

        return new static($amount);
    }

    /**
     * Вычесть сумму из текущей суммы.
     *
     * @param MoneyInterface $subtractedAmount Вычитаемая сумма
     * @return static Новый экземпляр типа с итоговой суммой
     */
    public function subtract(MoneyInterface $subtractedAmount): static
    {
        $amount = $this->amount - $subtractedAmount->asInteger();

        return new static($amount);
    }

    /**
     * Умножить сумму на заданное значение.
     *
     * Итоговое значение приводится к целочисленному виду.
     *
     * @param float|FloatKindNumberInterface $value Множитель суммы
     * @return static Новый экземпляр типа с итоговой суммой
     */
    public function multiplyBy(float|FloatKindNumberInterface $value): static
    {
        $multiplier = is_float($value) ? $value : $value->asFloat();
        $amount = (int) ($this->amount * $multiplier);

        return new static($amount);
    }

    /**
     * Разделить сумму на указанное значение.
     *
     * Итоговое значение приводится к целочисленному виду.
     *
     * @param float|FloatKindNumberInterface $value делитель суммы
     * @return static Новый экземпляр типа с итоговой суммой
     */
    public function divisionBy(float|FloatKindNumberInterface $value): static
    {
        $divider = is_float($value) ? $value : $value->asFloat();
        $amount = (int) ($this->amount / $divider);

        return new static($amount);
    }

    /**
     * Получить сумму соответствующую передаваемому проценту
     * от текущей суммы.
     *
     * Итоговое значение приводится к целочисленному виду.
     *
     * @param float|FloatKindNumberInterface $percent Вычисляемый процент
     * @return static Новый экземпляр типа с итоговой суммой
     */
    public function getPercent(float|FloatKindNumberInterface $percent): static
    {
        $percent = is_float($percent) ? $percent : $percent->asFloat();
        $amount = (int) ($this->amount / 100 * $percent);

        return new static($amount);
    }

    /**
     * Получить минимально допустимое значение.
     *
     * Если null, то ограничений на минимальное значение нет.
     *
     * @return int|null Минимальное поддерживаемое значение
     */
    public function getMinValue(): ?int
    {
        return $this->min;
    }

    /**
     * Получить максимально допустимое значение.
     *
     * Если null, то ограничений на максимальное значение нет.
     *
     * @return int|null Максимальное поддерживаемое значение
     */
    public function getMaxValue(): ?int
    {
        return $this->max;
    }

    /**
     * Валидация значения типа.
     *
     * @param int $amount Валидируемое значение
     */
    protected function validate(int $amount): void
    {
        try {
            $this->check()->thatAmountIsNotTooBig($amount);
            $this->check()->thatAmountIsNotTooSmall($amount);
        } catch (DomainException $e) {
            throw new FailedAmountCreationException(
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Получить валидатор типа.
     *
     * @return AmountValidator Экземпляр валидатора типа
     */
    protected function check(): AmountValidator
    {
        return $this->validator;
    }

    public function jsonSerialize(): string
    {
        return $this->asString();
    }
}
