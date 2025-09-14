<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Uuid\Validator;

use Dclouds\Ddd\ValueObjects\Uuid\Validator\Exceptions\InvalidUUIDFormatException;

/**
 * Валидатор типа Uuid
 */
readonly class UuidValidator
{
    /**
     * @param string $typeName Название типа
     */
    public function __construct(protected string $typeName)
    {
    }

    /**
     * Проверка соответствует ли передаваемая строка формату UUIDv4
     *
     * @param string $id Валидируемая строка
     */
    public function thatIdIsValidUUIDv4String(string $id): void
    {
        if (!$this->isValid($id)) {
            throw new InvalidUUIDFormatException(message: $this->typeName . 'имеет некорректный формат UUIDv4');
        }
    }

    /**
     * Проверяет, является ли переданный идентификатор ($id) корректным UUIDv4.
     *
     * @param string $id
     * @return bool результат проверка
     */
    protected function isValid(string $id): bool
    {
        $uuid = str_replace('-', '', $id);

        if (strlen($uuid) !== 32) {
            return false;
        }

        if ($uuid[12] !== '4') {
            return false;
        }

        $variant = $uuid[16];
        if (!in_array($variant, ['8', '9', 'A', 'B', 'a', 'b'], true)) {
            return false;
        }

        return true;
    }
}
