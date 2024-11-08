<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\FileSet;
use Iquety\Http\Session;
use Iquety\Shield\Assertion\EqualTo;
use Iquety\Shield\Assertion\IsEmail;
use Iquety\Shield\Field;
use Iquety\Shield\Shield;

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

    // public function contains(string $name, string $needle): self
    // {
    //     $this->shield()
    //         ->field($name)
    //         ->assert(new Contains($this->input($name), $needle));

    //     return $this;
    // }

    // public function endsWith(string $name, string $needle): self
    // {
    //     $this->shield()
    //         ->field($name)
    //         ->assert(new EndsWith($this->input($name), $needle));

    //     return $this;
    // }

    public function equalTo(string $needle): void
    {
        $value = (string)$this->param($this->currentFieldName);

        $assertion = new EqualTo($value, $needle);

        $this->currentField->assert($assertion);
    }

    // GreaterThan  The value is greater than the expected value
    // GreaterThanOrEqualTo     The value is greater than or equal to the expected
    // IsAlpha  The value contains only letters
    // IsAlphaNumeric   The value contains only letters and numbers
    // IsBase32     The value is a base32 encoded string
    // IsBase64     The value is a base64 encoded string
    // IsCreditCard     The value is a valid credit card
    // IsDate   The value is a valid date
    // IsDateTime   The value is a valid date and time

    public function isEmail(): void
    {
        $value = (string)$this->param($this->currentFieldName);

        $assertion = new IsEmail($value);

        $this->currentField->assert($assertion);
    }

    // IsEmpty  Value is empty
    // IsFalse  Value is false
    // IsHexadecimal    Value is a valid hexadecimal number
    // IsHexColor   Value is a valid hexadecimal color
    // IsIp     Value is a valid IP address
    // IsMacAddress     Value is a valid MAC address
    // IsNotEmpty   Value is not empty
    // IsNotNull    Value is not null
    // IsNull   Value is null
    // IsPhoneNumber    Value is a valid phone number
    // IsPostalCode     Value is a valid postal code
    // IsTime   Value is a valid time
    // IsTrue   Value is true
    // IsUrl    Value is a valid URL
    // IsUuid   Value is a valid UUID
    // Length   Value is the expected length
    // LessThan     Value is less than expected value
    // LessThanOrEqualTo    Value is less than or equal to expected value
    // Matches  Value matches expected pattern
    // MaxLength    Value has maximum expected length
    // MinLength    Value has at least minimum expected length
    // NotContains  Value does not contain expected value
    // NotEqualTo   Values ​​are different
    // NotMatches   Value does not match expected pattern
    // StartsWith   Value starts with expected value
}
