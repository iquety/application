<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Countable;
use Exception;
use InvalidArgumentException;
use Iquety\Application\IoEngine\ValueParser;
use LogicException;

class ValidableMap
{
    public function __construct(private string $methodName)
    {
    }

    /** @return array<string,mixed> */
    public function getAssertionValues(mixed $requestValue, mixed $routineValue): array
    {
        if (is_object($routineValue) === true) {
            throw new InvalidArgumentException('Argument is not valid');
        }

        $searchMethods = ['contains', 'notContains', 'endsWith', 'startsWith', 'matches', 'notMatches'];

        if (in_array($this->methodName, $searchMethods) === true) {
            return $this->makeSearchValues($requestValue, $routineValue);
        }

        $equalityMethods = ['equalTo', 'notEqualTo'];

        if (in_array($this->methodName, $equalityMethods) === true) {
            return $this->makeEqualityValue($requestValue, $routineValue);
        }

        $lengthMethods = ['length', 'maxLength', 'minLength' ];

        if (in_array($this->methodName, $lengthMethods) === true) {
            return $this->makeLengthValues($this->methodName, $requestValue, $routineValue);
        }

        $sizeMethods = ['greaterThan', 'greaterThanOrEqualTo', 'lessThan', 'lessThanOrEqualTo'];

        if (in_array($this->methodName, $sizeMethods) === true) {
            return $this->makeNumericSizeValues($this->methodName, $requestValue, $routineValue);
        }

        $nullableMethods = ['isNull', 'isNotNull'];

        if (in_array($this->methodName, $nullableMethods) === true) {
            throw new LogicException(sprintf("Method %s does not exist", $this->methodName));
        }

        // isAlpha
        // isAlphaNumeric
        // isAmountTime
        // isBase64
        // isBrPhoneNumber
        // isCep
        // isCpf
        // isCreditCard
        // isCreditCardBrand
        // isCvv
        // isDate
        // isDateTime
        // isEmail
        // isEmpty
        // isFalse
        // isHexadecimal
        // isHexColor
        // isIp
        // isMacAddress
        // isNotEmpty
        // isTime
        // isTrue
        // isUrl
        // isUuid

        return $this->makeFormatValue($requestValue);
    }

    /**
     * @param mixed $requestValue proveniente da requisição
     * @param mixed $routineValue proveniente da asserção programada
     * @return array<string,mixed>
     */
    private function makeEqualityValue(mixed $requestValue, mixed $routineValue): array
    {
        return [
            'valueOne' => (new ValueParser($requestValue))->withCorrectType(),
            'valueTwo' => (new ValueParser($routineValue))->withCorrectType()
        ];
    }

    /**
     * @param mixed $requestValue proveniente da requisição
     * @param mixed $routineValue proveniente da asserção programada
     * @return array<string,mixed>
     */
    private function makeSearchValues(mixed $requestValue, mixed $routineValue): array
    {
        if (is_array($requestValue) === true) {
            return [
                'valueOne' => $requestValue,
                'valueTwo' => (new ValueParser($routineValue))->withCorrectType()
            ];
        }

        return [
            'valueOne' => (string)$requestValue,
            'valueTwo' => (string)$routineValue
        ];
    }

    /**
     * @param mixed $requestValue proveniente da requisição
     * @param mixed $routineValue proveniente da asserção programada
     * @return array<string,mixed>
     */
    private function makeLengthValues(string $methodName, mixed $requestValue, mixed $routineValue): array
    {
        // $requestValue = (new ValueParser($requestValue))->withCorrectType();
        $routineValue = (new ValueParser($routineValue))->withCorrectType();

        if (is_numeric($routineValue) === false) {
            throw new InvalidArgumentException('Argument must be numeric');
        }

        if (is_array($requestValue) === true) {
            return [ 'valueOne' => $requestValue, 'valueTwo' => $routineValue ];
        }

        if (is_string($requestValue) === true) {
            return [ 'valueOne' => $requestValue, 'valueTwo' => $routineValue ];
        }

        // valores diferentes de string ou array devem ser inválidos
        switch ($methodName) {
            case 'length':
                $requestValue = str_repeat('a', (int)$routineValue + 1);
                break;

            case 'maxLength':
                $requestValue = str_repeat('a', (int)$routineValue + 1);
                break;

            case 'minLength':
                $requestValue = str_repeat('a', (int)$routineValue - 1);
                break;
        }

        return [ 'valueOne' => $requestValue, 'valueTwo' => $routineValue ];
    }

    /**
     * @param mixed $requestValue proveniente da requisição
     * @param mixed $routineValue proveniente da asserção programada
     * @return array<string,mixed>
     */
    private function makeNumericSizeValues(string $methodName, mixed $requestValue, mixed $routineValue): array
    {
        // $requestValue = (new ValueParser($requestValue))->withCorrectType();
        $routineValue = (new ValueParser($routineValue))->withCorrectType();

        if (is_numeric($routineValue) === false) {
            throw new InvalidArgumentException('Argument must be numeric');
        }

        if (
            $requestValue instanceof Countable
            || is_array($requestValue) === true
        ) {
            return [ 'valueOne' => $requestValue, 'valueTwo' => $routineValue ];
        }

        if (is_numeric($requestValue) === true) {
            return [ 'valueOne' => $requestValue, 'valueTwo' => $routineValue ];
        }

        // valores diferentes de numéricos, arrays ou Countable devem ser inválidos
        switch ($methodName) {
            case 'greaterThan':
                $requestValue = $routineValue - 1;
                break;
            case 'greaterThanOrEqualTo':
                $requestValue = $routineValue - 1;
                break;
            case 'lessThan':
                $requestValue = $routineValue + 1;
                break;
            case 'lessThanOrEqualTo':
                $requestValue = $routineValue + 1;
                break;
        };

        return [ 'valueOne' => (string)$requestValue, 'valueTwo' => $routineValue ];
    }

    /**
     * @param mixed $value proveniente da requisição
     * @return array<string,mixed>
     */
    private function makeFormatValue(mixed $value): array
    {
        $value = (new ValueParser($value))->withCorrectType();

        return [
            'valueOne' => $value,
            'valueTwo' => null
        ];
    }
}
