<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Closure;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\FileSet;
use Iquety\Http\Session;
use Iquety\Shield\Assertion\Contains;
use Iquety\Shield\Assertion\EndsWith;
use Iquety\Shield\Assertion\EqualTo;
use Iquety\Shield\Assertion\GreaterThan;
use Iquety\Shield\Assertion\GreaterThanOrEqualTo;
use Iquety\Shield\Assertion\IsAlpha;
use Iquety\Shield\Assertion\IsAlphaNumeric;
use Iquety\Shield\Assertion\IsBase64;
use Iquety\Shield\Assertion\IsBrPhoneNumber;
use Iquety\Shield\Assertion\IsCep;
use Iquety\Shield\Assertion\IsCreditCard;
use Iquety\Shield\Assertion\IsDate;
use Iquety\Shield\Assertion\IsDateTime;
use Iquety\Shield\Assertion\IsEmail;
use Iquety\Shield\Assertion\IsEmpty;
use Iquety\Shield\Assertion\IsFalse;
use Iquety\Shield\Assertion\IsHexadecimal;
use Iquety\Shield\Assertion\IsHexColor;
use Iquety\Shield\Assertion\IsIp;
use Iquety\Shield\Assertion\IsMacAddress;
use Iquety\Shield\Assertion\IsNotEmpty;
use Iquety\Shield\Assertion\IsNotNull;
use Iquety\Shield\Assertion\IsNull;
use Iquety\Shield\Assertion\IsTime;
use Iquety\Shield\Assertion\IsTrue;
use Iquety\Shield\Assertion\IsUrl;
use Iquety\Shield\Assertion\IsUuid;
use Iquety\Shield\Assertion\Length;
use Iquety\Shield\Assertion\LessThan;
use Iquety\Shield\Assertion\LessThanOrEqualTo;
use Iquety\Shield\Assertion\Matches;
use Iquety\Shield\Assertion\MaxLength;
use Iquety\Shield\Assertion\MinLength;
use Iquety\Shield\Assertion\NotContains;
use Iquety\Shield\Assertion\NotEqualTo;
use Iquety\Shield\Assertion\NotMatches;
use Iquety\Shield\Assertion\StartsWith;
use Iquety\Shield\Field;
use Iquety\Shield\Shield;
use LogicException;

/**
 * @method float|int|string|FileSet|null param()
 */
trait Validable
{
    private ?Shield $shield = null;

    private string $currentFieldName = '';

    private ?Field $currentField = null;

    private function shield(): Shield
    {
        if ($this->shield === null) {
            $this->shield = Application::instance()->make(Shield::class);
        }

        return $this->shield;
    }

    public function validOrRedirect(string $uri): void
    {
        try {
            $this->shield()->validOrThrow(AssertionFlashException::class);
        } catch (AssertionFlashException $exception) {
            $exception->setUri($uri);

            $this->flashErrors($exception->getErrorList());

            throw $exception;
        }
    }

    private function flashErrors(array $errorList): void
    {
        /** @var Session $session */
        $session = Application::instance()->make(Session::class);

        foreach ($errorList as $field => $messageList) {
            array_walk(
                $messageList,
                fn($message) =>$session->addFlash($field, $message)
            );
        }
    }

    public function validOrResponse(): void
    {
        $this->shield()->validOrThrow(AssertionResponseException::class);
    }

    public function assert(string $name): self
    {
        $this->currentFieldName = $name;

        $this->currentField = $this->shield()->field($name);

        return $this;
    }

    private function getTyped(mixed $value): mixed
    {
        if (is_bool($value) === true) {
            return (bool)$value;
        }

        if (is_int($value) === true) {
            return (int)$value;
        }

        if (is_float($value) === true) {
            return (float)$value;
        }

        if (is_null($value) === true) {
            return null;
        }

        return (string) $value;
    }

    private function assertFluency(): void
    {
        if($this->currentField === null) {
            throw new LogicException('You need to start with the assert() method');
        }
    }

    private function endFluency(): void
    {
        $this->currentField = null;

        $this->currentFieldName = '';
    }

    private function makeAssertion(Closure $assertionFactory): void
    {
        $this->assertFluency();

        $value = (string)$this->param($this->currentFieldName);

        $assertion = $assertionFactory($value);

        $this->currentField->assert($assertion);

        $this->endFluency();
    }

    public function contains(string $needle): void
    {
        $this->makeAssertion(fn($value) => new Contains($value, $needle));
    }

    public function endsWith(string $needle): void
    {
        $this->makeAssertion(fn($value) => new EndsWith($value, $needle));
    }

    public function equalTo(mixed $needle): void
    {
        $this->makeAssertion(
            fn($value) => new EqualTo($this->getTyped($value), $needle)
        );
    }

    public function greaterThan(mixed $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new GreaterThan($this->getTyped($value), $lenght)
        );
    }

    public function greaterThanOrEqualTo(float|int $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new GreaterThanOrEqualTo($this->getTyped($value), $lenght)
        );
    }

    public function isAlpha(): void
    {
        $this->makeAssertion(fn($value) => new IsAlpha($value));
    }

    public function isAlphaNumeric(): void
    {
        $this->makeAssertion(fn($value) => new IsAlphaNumeric($value));

    }

    public function isBase64(): void
    {
        $this->makeAssertion(fn($value) => new IsBase64($value));

    }

    public function isCreditCard(): void
    {
        $this->makeAssertion(fn($value) => new IsCreditCard($value));
    }

    public function isDate(): void
    {
        $this->makeAssertion(fn($value) => new IsDate($value));
    }

    public function isDateTime(): void
    {
        $this->makeAssertion(fn($value) => new IsDateTime($value));
    }

    public function isEmail(): void
    {
        $this->makeAssertion(fn($value) => new IsEmail($value));
    }

    public function isEmpty(): void
    {
        $this->makeAssertion(
            fn($value) => new IsEmpty($this->getTyped($value))
        );
    }

    public function isFalse(): void
    {
        $this->makeAssertion(
            fn($value) => new IsFalse($this->getTyped($value))
        );
    }

    public function isHexadecimal(): void
    {
        $this->makeAssertion(fn($value) => new IsHexadecimal($value));
    }

    public function isHexColor(): void
    {
        $this->makeAssertion(fn($value) => new IsHexColor($value));
    }

    public function isIp(): void
    {
        $this->makeAssertion(fn($value) => new IsIp($value));
    }

    public function isMacAddress(): void
    {
        $this->makeAssertion(fn($value) => new IsMacAddress($value));
    }

    public function isNotEmpty(): void
    {
        $this->makeAssertion(
            fn($value) => new IsNotEmpty($this->getTyped($value))
        );
    }

    public function isNotNull(): void
    {
        $this->makeAssertion(
            fn($value) => new IsNotNull($this->getTyped($value))
        );
    }

    public function isNull(): void
    {
        $this->makeAssertion(
            fn($value) => new IsNull($this->getTyped($value))
        );
    }

    public function isBrPhoneNumber(): void
    {
        $this->makeAssertion(fn($value) => new IsBrPhoneNumber($value));
    }

    public function isCep(): void
    {
        $this->makeAssertion(fn($value) => new IsCep($value));
    }

    public function isTime(): void
    {
        $this->makeAssertion(fn($value) => new IsTime($value));
    }

    public function isTrue(): void
    {
        $this->makeAssertion(
            fn($value) => new IsTrue($this->getTyped($value))
        );
    }

    public function isUrl(): void
    {
        $this->makeAssertion(fn($value) => new IsUrl($value));
    }

    public function isUuid(): void
    {
        $this->makeAssertion(fn($value) => new IsUuid($value));
    }

    public function length(float|int $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new Length($this->getTyped($value), $lenght)
        );
    }

    public function lessThan(float|int $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new LessThan($this->getTyped($value), $lenght)
        );
    }

    public function lessThanOrEqualTo(float|int $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new LessThanOrEqualTo($this->getTyped($value), $lenght)
        );
    }

    public function matches(string $pattern): void
    {
        $this->makeAssertion(fn($value) => new Matches($value, $pattern));
    }

    public function maxLength(float|int $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new MaxLength($this->getTyped($value), $lenght)
        );
    }

    public function minLength(float|int $lenght): void
    {
        $this->makeAssertion(
            fn($value) => new MinLength($this->getTyped($value), $lenght)
        );
    }

    public function notContains(string $needle): void
    {
        $this->makeAssertion(fn($value) => new NotContains($value, $needle));
    }

    public function notEqualTo(mixed $needle): void
    {
        $this->makeAssertion(
            fn($value) => new NotEqualTo($this->getTyped($value), $needle)
        );
    }

    public function notMatches(string $pattern): void
    {
        $this->makeAssertion(fn($value) => new NotMatches($value, $pattern));
    }

    public function startsWith(string $needle): void
    {
        $this->makeAssertion(fn($value) => new StartsWith($value, $needle));
    }
}
