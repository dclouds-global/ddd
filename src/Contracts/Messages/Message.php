<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Messages;

/**
 * Интерфейс структуры сообщения
 */
interface Message
{
    /**
     * Тело сообщения
     *
     * @var array<array-key, mixed>
     */
    public array $body {get;}

    /**
     * Заголовки сообщения
     *
     * @var array<string, mixed>
     */
    public array $headers {get;}

    /**
     * Дополнительные параметры сообщения
     *
     * @var array<string, mixed>
     */
    public array $additionalParams {get;}
}
