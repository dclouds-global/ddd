<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Transactions;

/**
 * Транзакция БД
 */
interface TransactionInterface
{
    /**
     * Начать транзакцию
     *
     * @return void
     */
    public function begin(): void;

    /**
     * Завершить транзакцию
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Откатить транзакцию
     *
     * @return void
     */
    public function rollback(): void;
}
