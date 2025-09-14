<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Email;

use Dclouds\Ddd\ValueObjects\Email\Exceptions\FailedEmailCreationException;
use Dclouds\Ddd\ValueObjects\Email\Validator\EmailValidator;
use DomainException;
use JsonSerializable;

/**
 * тип Email
 */
class Email implements JsonSerializable
{
    /**
     * Название типа
     */
    protected string $typeName = 'Email';

    /**
     * Валидатор типа
     */
    protected EmailValidator $validator;

    /**
     * Создание нового Email
     *
     * @param string $email строковое представление типа
     */
    public function __construct(protected readonly string $email)
    {
        $this->validator = new EmailValidator($this->typeName);
        $this->validate($email);
    }

    /**
     * Получить строковой скалярное представление типа
     *
     * @return string Скалярное представление
     */
    public function toString(): string
    {
        return $this->email;
    }

    /**
     * Валидация скалярного значения
     *
     * @param string $email валидируемая строка
     */
    protected function validate(string $email): void
    {
        try {
            $this->check()->thatEmailHasValidAddress($email);
        } catch (DomainException $e) {
            throw new FailedEmailCreationException(
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Получить экземпляр валидатора
     *
     * @return EmailValidator Экземпляр валидатора
     */
    protected function check(): EmailValidator
    {
        return $this->validator;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }
}
