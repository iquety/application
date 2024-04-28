<?php

declare(strict_types=1);

namespace Iquety\Application\Domain;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Iquety\Application\Application;
use Iquety\Application\Configuration;
use Iquety\PubSub\Event\Event;
use ReflectionClass;
use ReflectionObject;

abstract class DomainEvent implements Event
{
    protected ?DateTimeImmutable $ocurredOn = null;

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        $className = get_called_class();

        $values = self::resolveDateTimes($values);

        $arguments = self::makeConstructorArguments($className, $values);

        $event = new $className(...$arguments);

        $reflection = new ReflectionObject($event);
        
        $property = $reflection->getProperty('ocurredOn');
        $property->setAccessible(true);
        $property->setValue($event, $values['ocurredOn']);

        return $event;
    }

    private static function resolveDateTimes(array $valueList): array
    {
        foreach ($valueList as $name => $value) {
            if ($value instanceof DateTime) {
                $valueList[$name] = new DateTimeImmutable(
                    $value->format('Y-m-d H:i:s'),
                    $value->getTimezone() ?: null
                );
            }
        }

        return $valueList;
    }

    private static function makeConstructorArguments(string $className, array $values): array
    {
        $reflection = new ReflectionClass($className);
        
        $argumentList = $reflection->getConstructor()->getParameters();

        $list = [];

        foreach($argumentList as $argument) {
            $label = $argument->getName();

            $list[] = $values[$label];
        }

        return $list;
    }

    abstract public function label(): string;

    public function ocurredOn(): DateTimeImmutable
    {
        if ($this->ocurredOn === null) {
            $this->ocurredOn = new DateTimeImmutable(
                'now',
                Configuration::instance()->get('timezone')
            );
        }

        return $this->ocurredOn;
    }

    public function sameEventAs(Event $other): bool
    {
        $className = get_called_class();

        return $other instanceof $className
            && $this->toArray() == $other->toArray();
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        $reflection = new ReflectionObject($this);

        $argumentList = $reflection->getConstructor()->getParameters();

        $propertyList = [];

        foreach($argumentList as $argument) {
            $label = $argument->getName();

            $property = $reflection->getProperty($label);
            $property->setAccessible(true);
            
            $value = $property->getValue($this);

            $propertyList[$label] = $value;
        }

        $propertyList['ocurredOn'] = $this->ocurredOn();

        return $propertyList;
    }
}
