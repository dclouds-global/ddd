# DDD - Библиотека Domain-Driven Design для PHP

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Набор базовых объектов для реализации Domain-Driven Design (DDD) паттернов в PHP приложениях. Библиотека предоставляет готовые реализации агрегатов, сущностей, объектов-значений и других строительных блоков для создания качественной доменной модели.

### Установка через Composer

```bash
composer require dclouds/ddd
```

## Основные компоненты

### Агрегаты

Агрегаты представляют основные бизнес-объекты в системе и управляют потоком событий предметной области.

#### Ключевые особенности:

- **Event Sourcing**: Фиксация и применение событий для изменения состояния
- **Domain Events**: События предметной области для внутренней логики
- **Integration Events**: События для интеграции с внешними системами
- **Версионность**: Контроль версий агрегатов для консистентности

#### Создание агрегата:

```php
<?php

use Dclouds\Ddd\Aggregate\AbstractAggregateRoot;
use Dclouds\Ddd\ValueObjects\Uuid\Uuid;

class UserAggregate extends AbstractAggregateRoot
{
    public function __construct(
        protected(set) Uuid $id,
        protected(set) Text $name,
        protected(set) Email $email,
    ) {
        //
    }

    protected function validate(): void
    {
        // Проверка бизнес-правил агрегата
    }
}
```

### Объекты-значения (Value Objects)

Immutable объекты, представляющие концепции предметной области.

#### Доступные Value Objects:

1. **UUID** - Уникальные идентификаторы с поддержкой временного упорядочивания
2. **Email** - Email адреса с валидацией
3. **Amount** - Денежные суммы с арифметическими операциями
4. **Text** - Текстовые значения с ограничениями по длине
5. **Integer** - Целые числа с диапазонами
6. **Double** - Числа с плавающей точкой

#### Пример использования UUID:

```php
<?php

use Dclouds\Ddd\ValueObjects\Uuid\Uuid;

class UserId extends Uuid
{
    protected string $typeName = 'Идентификатор пользователя';
}

class UserEmail extends Email
{
    protected string $typeName = 'E-mail пользователя';
}

class ProductPrice extends Amount
{
    protected string $typeName = 'Сумма товара';

    protected ?int $min = 100;
}

class ProductName extends Text
{
    protected string $typeName = 'Название товара';

    protected ?int $min = 5;

    protected ?int $max = 255;
}
```

### Сущности

Базовый класс для бизнес-сущностей, которые могут изменяться в рамках агрегата.

```php
<?php

use Dclouds\Ddd\Entity\AbstractEntity;

class OrderItem extends AbstractEntity
{
    protected string $aggregateClass = OrderAggregate::class;

    public function __construct(
        protected(set) Uuid $productId,
        protected(set) Integer $quantity,
        protected(set) Amount $price,
    ) {
        //
    }

    // Применение событий только в контексте агрегата
    public function applyQuantityChanged(QuantityChangedEvent $event): void
    {
        $this->quantity = $event->newQuantity;
    }
}
```

### Outbox Pattern

Реализация паттерна Outbox для надежной доставки интеграционных событий.

#### Ключевые особенности:

- Атомарная запись событий в рамках транзакции
- Статусы обработки событий
- Повторные попытки при ошибках
- Очистка старых успешных событий

```php
<?php

use Dclouds\Ddd\Contracts\Outbox\OutboxServiceInterface;

class EventDispatcher
{
    public function __construct(
        private OutboxServiceInterface $outboxService
    ) {
        //
    }

    public function dispatch(AbstractAggregateRoot $aggregate): void
    {
        // Получение интеграционных событий
        $events = $aggregate->releaseIntegrationEvents();

        // Регистрация в Outbox
        foreach ($events as $event) {
            $this->outboxService->registerEvent($event);
        }
    }
}
```

### Пагинация

Удобные инструменты для работы с пагинацией результатов.

```php
<?php

use Dclouds\Ddd\Paginators\Paginator;
use Dclouds\Ddd\Paginators\PageInfo;

// Создание информации о странице
$pageInfo = new PageInfo(
    page: 2,
    pageSize: 20,
    totalElements: 1000
);

// Создание пагинатора
$paginator = new Paginator($pageInfo);

echo "Текущая страница: " . $paginator->pageInfo->page;
echo "Всего страниц: " . $paginator->pageInfo->totalPages;
echo "Есть следующая страница: " . ($paginator->pageInfo->hasNext ? 'Да' : 'Нет');
```

## Лицензия

Библиотека распространяется под MIT лицензией.

## Автор

- **Алексей Нечаев** - omfg.rus@gmail.com

---

*Эта библиотека создана для упрощения реализации DDD паттернов в PHP приложениях и предоставляет надежную основу для построения качественной доменной модели.*
