<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Transactions;

/**
 * Фабрика транзакций БД.
 */
interface TransactionFactoryInterface
{
    /**
     * Получить новый объект транзакции
     *
     * @return TransactionInterface
     */
    public function new(): TransactionInterface;
}
