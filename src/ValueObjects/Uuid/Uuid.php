<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Uuid;

use Dclouds\Ddd\ValueObjects\Uuid\Exceptions\FailedUuidCreationException;
use Dclouds\Ddd\ValueObjects\Uuid\Validator\UuidValidator;
use DomainException;
use JsonSerializable;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\UuidFactory;

/**
 * Universal Unique Identifier v4 (поддерживает порядок)
 */
class Uuid implements JsonSerializable
{
    /**
     * Название типа
     */
    protected string $typeName = 'Идентификатор';

    /**
     * Валидатор значения
     */
    protected UuidValidator $validator;

    /**
     * Создание нового экземпляра UUIDv4 на основе строкового значения
     *
     * @param string $id строковое представление UUIDv4
     */
    final public function __construct(protected readonly string $id)
    {
        $this->validator = new UuidValidator($this->getTypeName());
        $this->validate($id);
    }

    /**
     * Сгенерировать новый UUIDv4 на основе текущей метки времени
     *
     * @return static UUIDv4
     */
    public static function generate(): static
    {
        $factory = new UuidFactory();

        $factory->setRandomGenerator(new CombGenerator(
            $factory->getRandomGenerator(),
            $factory->getNumberConverter()
        ));

        $factory->setCodec(new TimestampFirstCombCodec(
            $factory->getUuidBuilder()
        ));

        return new static($factory->uuid4()->toString());
    }

    /**
     * Получить скалярное строковое представление типа
     *
     * @return string UUIDv4
     */
    public function asString(): string
    {
        return $this->id;
    }

    /**
     * Сравнивает значение текущего экземпляра со значением другого
     * экземпляра, либо со строковым представлением. Передаваемая
     * строка при этом должна иметь корректный формат UUIDv4, в противном
     * случае будет выброшено исключение.
     *
     * @param string|Uuid $comparable сравниваемое значение
     * @return bool Вернет true, если значения соответствуют дру-другу
     */
    public function isEqual(string|Uuid $comparable): bool
    {
        if (is_string($comparable)) {
            $this->validate($comparable);
            return $this->asString() === $comparable;
        }

        return $this->asString() === $comparable->asString();
    }

    /**
     * Сравнивает значение текущего экземпляра со значением другого
     * экземпляра, либо со строковым представлением. Передаваемая
     * строка при этом должна иметь корректный формат UUIDv4, в противном
     * случае будет выброшено исключение.
     *
     * @param string|Uuid $comparable сравниваемое значение
     * @return bool Вернет true, если значения не соответствуют дру-другу
     */
    public function isNotEqual(string|Uuid $comparable): bool
    {
        if (is_string($comparable)) {
            $this->validate($comparable);
            return $this->asString() !== $comparable;
        }

        return $this->asString() !== $comparable->asString();
    }

    /**
     * Сравнивает между собой пару UUID по времени их создания. Так как экземпляр
     * генерируется на основе метки времени, каждый UUID может быть упорядочен.
     *
     * @param Uuid $comparable сравниваемое значение
     * @return bool вернет true, если текущий UUID был создан позднее, чем сравниваемый
     */
    public function isNewerThan(Uuid $comparable): bool
    {
        return $this->asString() > $comparable->asString();
    }

    /**
     * Сравнивает между собой пару UUID по времени их создания. Так как экземпляр
     * генерируется на основе метки времени, каждый UUID может быть упорядочен.
     *
     * @param Uuid $comparable сравниваемое значение
     * @return bool вернет true, если текущий UUID был создан раньше, чем сравниваемый
     */
    public function isOlderThan(Uuid $comparable): bool
    {
        return $this->asString() < $comparable->asString();
    }

    /**
     * Возвращает название типа
     *
     * @return string Название типа
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * Выполнение правил валидации значения
     *
     * @param string $id Валидируемое значение
     */
    protected function validate(string $id): void
    {
        try {
            $this->check()->thatIdIsValidUUIDv4String($id);
        } catch (DomainException $e) {
            throw new FailedUuidCreationException(
                message: $this->typeName . ': Ошибка валидации',
                previous: $e
            );
        }
    }

    /**
     * Получит экземпляр валидатора
     *
     * @return UuidValidator экземпляр валидатора
     */
    protected function check(): UuidValidator
    {
        return $this->validator;
    }

    public function jsonSerialize(): string
    {
        return $this->asString();
    }
}
