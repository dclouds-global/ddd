<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Email\Validator;

use Dclouds\Ddd\ValueObjects\Email\Validator\Exceptions\EmailMustHasValidAddressException;

/**
 * Валидатор типа Email
 */
readonly class EmailValidator
{
    /**
     * @param string $typeName Название типа
     */
    public function __construct(protected string $typeName)
    {
    }

    /**
     * Устанавливает, является ли строка корректным email адресом.
     *
     * @param string $email Проверяемый Email
     */
    public function thatEmailHasValidAddress(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            throw new EmailMustHasValidAddressException($this->typeName . ': некорректный формат');
        }
    }
}
