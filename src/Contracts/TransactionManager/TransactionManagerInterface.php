<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\TransactionManager;

use Closure;

/**
 * Менеджер транзакций.
 *
 * Нужно использовать когда нужна гарантия, что при сохранении агрегата
 * будет выполнена дополнительная логика (например регистрация событий),
 * а в случае ошибки при выполнении данной логики созданный или измененный
 * агрегат не должен быть сохранен на уровне хранилища (БД).
 *
 * @param Closure $callback функциональность выполняемая в рамках транзакции
 * @param positive-int $attempts количество попыток выполнения транзакции
 */
interface TransactionManagerInterface
{
    public function transaction(Closure $callback, int $attempts = 1): void;
}
