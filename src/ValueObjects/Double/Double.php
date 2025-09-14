<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Double;

use Dclouds\Ddd\Contracts\ValueObjects\ComparableInterface;
use Dclouds\Ddd\Contracts\ValueObjects\FloatKindNumberInterface;
use Dclouds\Ddd\ValueObjects\Double\Exceptions\FailedDoubleCreationException;
use Dclouds\Ddd\ValueObjects\Double\Validator\DoubleValidator;
use DomainException;
use JsonSerializable;

/**
 * Число с плавающей точкой с ограничениями
 */
class Double implements FloatKindNumberInterface, JsonSerializable
{
    /**
     * Название типа
     */
    protected string $typeName = 'Число с плавающей точкой';

    /**
     * Минимально допустимое значение.
     *
     * Если задано null, минимальное значение отсутствует.
     */
    protected ?float $min = null;

    /**
     * Максимально допустимое значение.
     *
     * Если задано null, максимальное значение отсутствует.
     */
    protected ?float $max = null;

    protected DoubleValidator $validator;

    /**
     * Создание нового экземпляра типа Double
     *
     * @param float $double дробное представление типа
     */
    public function __construct(private readonly float $double)
    {
        $this->validator = new DoubleValidator(
            typeName: $this->typeName,
            min: $this->min,
            max: $this->max
        );
        $this->validate($double);
    }

    /**
     * Получить скалярное представление типа в виде
     * числа с плавающей точкой
     *
     * @return float Скалярное представление
     */
    public function asFloat(): float
    {
        return $this->double;
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
            num: $this->double,
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
            return $this->double > $other;
        }

        return $other->isLessThan($this->double);
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
            return $this->double < $other;
        }

        return $other->isMoreThan($this->double);
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
            return $this->double === $other;
        }

        return $other->isEqual($this->double);
    }

    /**
     * Добавить новое значение к текущему.
     *
     * @param float|FloatKindNumberInterface $additionalDouble Добавляемое значение
     * @return static Новый экземпляр типа с итоговым значением
     */
    public function add(float|FloatKindNumberInterface $additionalDouble): static
    {
        $additional = is_float($additionalDouble) ? $additionalDouble : $additionalDouble->asFloat();
        $double = $this->double + $additional;

        return new static($double);
    }

    /**
     * Вычесть новое значение из текущего.
     *
     * @param float|FloatKindNumberInterface $subtractedDouble Вычитаемое значение
     * @return static Новый экземпляр типа с итоговым значением
     */
    public function subtract(float|FloatKindNumberInterface $subtractedDouble): static
    {
        $subtracted = is_float($subtractedDouble) ? $subtractedDouble : $subtractedDouble->asFloat();
        $double = $this->double - $subtracted;

        return new static($double);
    }

    /**
     * Умножить текущее значение на указанное значение.
     *
     * @param float|FloatKindNumberInterface $value Множитель
     * @return static Новый экземпляр типа с итоговым значением
     */
    public function multiplyBy(float|FloatKindNumberInterface $value): static
    {
        $multiplier = is_float($value) ? $value : $value->asFloat();
        $double = $this->double * $multiplier;

        return new static($double);
    }

    /**
     * Разделить текущее значение на указанное значение.
     *
     * @param float|FloatKindNumberInterface $value Делитель
     * @return static Новый экземпляр типа с итоговым значением
     */
    public function divisionBy(float|FloatKindNumberInterface $value): static
    {
        $divider = is_float($value) ? $value : $value->asFloat();
        $double = $this->double / $divider;

        return new static($double);
    }

    /**
     * Получить значение соответствующее передаваемому проценту
     * от текущего.
     *
     * @param float|FloatKindNumberInterface $percent Вычисляемый процент
     * @return static Новый экземпляр типа с итоговым значением
     */
    public function getPercent(float|FloatKindNumberInterface $percent): static
    {
        $percent = is_float($percent) ? $percent : $percent->asFloat();
        $double = $this->double / 100 * $percent;

        return new static($double);
    }

    /**
     * Получить минимально допустимое значение.
     *
     * Если null, то ограничений на минимальное значение нет.
     *
     * @return null|float Минимальное поддерживаемое значение
     */
    public function getMinValue(): ?float
    {
        return $this->min;
    }

    /**
     * Получить максимально допустимое значение.
     *
     * Если null, то ограничений на максимальное значение нет.
     *
     * @return null|float Максимальное поддерживаемое значение
     */
    public function getMaxValue(): ?float
    {
        return $this->max;
    }

    /**
     * Валидация значения типа.
     *
     * @param float $amount Валидируемое значение
     */
    protected function validate(float $amount): void
    {
        try {
            $this->check()->thatValueIsNotTooBig($amount);
            $this->check()->thatValueIsNotTooSmall($amount);
        } catch (DomainException $e) {
            throw new FailedDoubleCreationException(
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Получить валидатор типа
     *
     * @return DoubleValidator экземпляр валидатора
     */
    protected function check(): DoubleValidator
    {
        return $this->validator;
    }

    public function jsonSerialize(): float
    {
        return $this->asFloat();
    }
}
