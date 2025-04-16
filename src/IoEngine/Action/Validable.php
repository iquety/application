<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use InvalidArgumentException;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\FileSet;
use Iquety\Http\Session;
use Iquety\Shield\Assertion;
use Iquety\Shield\Field;
use Iquety\Shield\Shield;
use LogicException;
use RuntimeException;
use Throwable;

/**
 * @method float|int|string|FileSet|null param(int|string $param)
 * @method self message(string $pattern)
 * @method self contains(string $needle)
 * @method self endsWith(string $needle)
 * @method self equalTo(mixed $needle)
 * @method self greaterThan(int $length)
 * @method self greaterThanOrEqualTo(int $length)
 * @method self isAlpha()
 * @method self isAlphaNumeric()
 * @method self isAmountTime()
 * @method self isBase64()
 * @method self isBrPhoneNumber()
 * @method self isCep()
 * @method self isCpf()
 * @method self isCreditCard()
 * @method self isCreditCardBrand()
 * @method self isCvv()
 * @method self isDate()
 * @method self isDateTime()
 * @method self isEmail()
 * @method self isEmpty()
 * @method self isFalse()
 * @method self isHexColor()
 * @method self isHexadecimal()
 * @method self isIp()
 * @method self isMacAddress()
 * @method self isNotEmpty()
 * @method self isNotNull()
 * @method self isNull()
 * @method self isTime()
 * @method self isTrue()
 * @method self isUrl()
 * @method self isUuid()
 * @method self length(int $length)
 * @method self lessThan(int $length)
 * @method self lessThanOrEqualTo(int $length)
 * @method self matches(string $needle)
 * @method self maxLength(int $length)
 * @method self minLength(int $length)
 * @method self notContains(string $needle)
 * @method self notEqualTo(mixed $needle)
 * @method self notMatches(string $needle)
 * @method self startsWith(string $needle)
 */
trait Validable
{
    private ?Shield $shield = null;

    private string $currentFieldName = '';

    private ?Field $currentField = null;

    private ?Assertion $currentAssertion = null;

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

    /** @param array<int|string,array<int,string>|string> $errorList */
    private function flashErrors(array $errorList): void
    {
        /** @var Session $session */
        $session = Application::instance()->make(Session::class);

        /**
         * @var string $field nome do campo
         * @var array<int,string> $messageList mensagens de erro no campo
         */
        foreach ($errorList as $field => $messageList) {
            array_walk(
                $messageList,
                fn($message) => $session->addFlash($field, $message)
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

    /** @param array<int,mixed> $argumentList */
    public function __call(string $method, array $argumentList): self
    {
        $methodMap = new ValidableMap($method);

        $this->startFluency();

        if ($method === 'message') {
            $this->currentAssertion?->message(...$argumentList);

            return $this;
        }

        try {
            $className = $this->makeAssertionClassName($method);

            // método param está em Input
            $fieldValue = $this->param($this->currentFieldName);

            if ($fieldValue === null) {
                throw new InvalidArgumentException("Field '$this->currentFieldName' does not exist");
            }

            $values = $methodMap->getAssertionValues(
                $fieldValue,
                $argumentList[0] ?? ''
            );

            $valueOne = $values['valueOne'];
            $valueTwo = $values['valueTwo'];

            /** @var Assertion $assertion */
            $assertion = new $className($valueOne, $valueTwo);

            $this->currentAssertion = $this->currentField?->assert($assertion);

            return $this;
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new RuntimeException(sprintf(
                "%s on %s in line %d",
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));
        }

        // $this->endFluency();
    }

    private function makeAssertionClassName(string $method): string
    {
        $className = "\Iquety\Shield\Assertion\\" . ucfirst($method);

        if (class_exists($className) === false) {
            throw new LogicException("Method $method does not exist");
        }

        return $className;
    }

    private function startFluency(): void
    {
        if ($this->currentField === null) {
            throw new LogicException('You need to start with the assert() method');
        }
    }

    // private function endFluency(): void
    // {
    //     $this->currentField = null;

    //     $this->currentFieldName = '';
    // }
}
