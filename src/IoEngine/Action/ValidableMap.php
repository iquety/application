<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use InvalidArgumentException;
use Iquety\Application\IoEngine\ValueParser;

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

        $containsMethods = ['contains', 'notContains', 'endsWith', 'startsWith', 'matches', 'notMatches'];

        if (in_array($this->methodName, $containsMethods) === true) {
            return $this->makeSearchValues($requestValue, $routineValue);
        }

        $equalityMethods = ['equalTo', 'notEqualTo'];

        if (in_array($this->methodName, $equalityMethods) === true) {
            return $this->makeEqualityValue($requestValue, $routineValue);
        }

        $lengthMethods = ['length', 'maxLength', 'minLength' ];

        if (in_array($this->methodName, $lengthMethods) === true) {
            return $this->makeLengthValues($requestValue, $routineValue);
        }

        $sizeMethods = ['greaterThan', 'greaterThanOrEqualTo', 'lessThan', 'lessThanOrEqualTo'];

        if (in_array($this->methodName, $sizeMethods) === true) {
            return $this->makeLengthValues($requestValue, $routineValue);
        }
        
        // format methods
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
        // isNotNull
        // isNull
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
     * @param string $requestValue proveniente da requisição
     * @param string $routineValue proveniente da asserção programada
     * @return array<string,mixed>
     */
    private function makeSearchValues(mixed $requestValue, mixed $routineValue): array
    {
        if (is_array($requestValue) === true) {
            return [ 'valueOne' => array_map(fn($value) => (string)$value, $requestValue), 'valueTwo' => (string)$routineValue ];
        }

        return [ 'valueOne' => (string)$requestValue, 'valueTwo' => (string)$routineValue ];
    }

    /**
     * @param mixed $requestValue proveniente da requisição
     * @param mixed $routineValue proveniente da asserção programada
     * @return array<string,mixed>
     */
    private function makeLengthValues(mixed $requestValue, mixed $routineValue): array
    {
        $requestValue = (new ValueParser($requestValue))->withCorrectType();
        $routineValue = (new ValueParser($routineValue))->withCorrectType();

        if (is_numeric($routineValue) === false) {
            throw new InvalidArgumentException('Argument must be numeric');
        }

        return [ 'valueOne' => $requestValue, 'valueTwo' => $routineValue ];
    }

    /**
     * @param mixed $requestValue proveniente da requisição
     * @return array<string,mixed>
     */
    private function makeFormatValue(mixed $value): array
    {
        return [
            'valueOne' => (new ValueParser($value))->withCorrectType(),
            'valueTwo' => null
        ];
    }
}
