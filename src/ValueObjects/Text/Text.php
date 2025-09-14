<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Text;

use Dclouds\Ddd\ValueObjects\Text\Exceptions\TextHasWrongLengthException;
use Dclouds\Ddd\ValueObjects\Text\Exceptions\TextIsTooLongException;
use JsonSerializable;

class Text implements JsonSerializable
{
    protected string $typeName = 'Текст';
    protected ?int $min = null;
    protected ?int $max = null;

    public function __construct(private string $text)
    {
        if (!is_null($this->min) && !is_null($this->max) && $this->min > $this->max) {
            throw new TextHasWrongLengthException(
                message: $this->typeName . ' не может иметь значение минимального количества символов, превышающее максимальное'
            );
        }

        if (!is_null($this->max) && mb_strlen($this->text) > $this->max) {
            throw new TextIsTooLongException(
                message: $this->typeName . ' превышает максимально допустимое количество символов (' . $this->max . ')'
            );
        }

        if (!is_null($this->min) & mb_strlen($this->text) < $this->min) {
            throw new TextIsTooLongException(
                message: $this->typeName . ' не содержит минимально допустимого количества символов (' . $this->min . ')'
            );
        }
    }

    public function asString(): string
    {
        return $this->text;
    }

    public function jsonSerialize(): string
    {
        return $this->asString();
    }
}
