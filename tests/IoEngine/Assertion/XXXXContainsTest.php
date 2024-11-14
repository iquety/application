<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Validable;
use Iquety\Shield\Shield;
use LogicException;
use Tests\TestCase;

class ValidableTest extends AssertionCase
{
    /** @return array<string,array<int,mixed>> */
    public function assertionsValidProvider(): array
    {
        $list = [];

        // $list['string contains'] = ['contains', 'Palavra', 'lavr'];
        // $list['string endsWith'] = ['endsWith', 'Palavra', 'vra'];

        // $list['string equalTo'] = ['equalTo', 'one', 'one'];
        $list['int equalTo'] = ['equalTo', '12', 12];
        // $list['float equalTo'] = ['equalTo', '0.4', 0.4];

        // $list['greaterThan'] = ['greaterThan', 'Palavra', 6];
        // $list['greaterThanOrEqualTo'] = ['greaterThanOrEqualTo', 'Palavra', 7];
        // $list['isAlpha'] = ['isAlpha', 'Palavra', null];
        // $list['isAlphaNumeric'] = ['isAlphaNumeric', 'Palavra 44', null];
        // $list['isBase64'] = ['isBase64', base64_encode('xx'), null];
        // $list['isCreditCard'] = ['isCreditCard', '4810021922532013', null];
        // $list['isDate'] = ['isDate', '10/10/2000', null];
        // $list['isDateTime'] = ['isDateTime', '10/10/2000 00:00:00', null];
        // $list['isEmail'] = ['isEmail', 'contato@gmail.com', null];
        // $list['isEmpty'] = ['isEmpty', '', null];
        // $list['isFalse'] = ['isFalse', 'false', null];
        // // $list['isHexadecimal'] = ['isHexadecimal', 'two', null];
        // $list['isHexColor'] = ['isHexColor', '#ff00ff', null];
        // $list['isIp'] = ['isIp', 'two', null];
        // $list['isMacAddress'] = ['isMacAddress', 'two', null];
        // $list['isNotEmpty'] = ['isNotEmpty', 'xxx', null];
        // $list['isNotNull'] = ['isNotNull', 'xxx', null];
        // $list['isNull'] = ['isNull', 'null', null];
        // $list['isBrPhoneNumber'] = ['isBrPhoneNumber', 'two', null];
        // $list['isCep'] = ['isCep', 'two', null];
        // $list['isTime'] = ['isTime', 'two', null];
        // $list['isTrue'] = ['isTrue', 'two', null];
        // $list['isUrl'] = ['isUrl', 'two', null];
        // $list['isUuid'] = ['isUuid', 'two', null];
        // $list['length'] = ['length', 'two', null];
        // $list['lessThan'] = ['lessThan', 'two', null];
        // $list['lessThanOrEqualTo'] = ['lessThanOrEqualTo', 'two', null];
        // $list['matches'] = ['matches', 'two', null];
        // $list['maxLength'] = ['maxLength', 'two', null];
        // $list['minLength'] = ['minLength', 'two', null];
        // $list['notContains'] = ['notContains', 'two', null];
        // $list['notEqualTo'] = ['notEqualTo', 'two', null];
        // $list['notMatches'] = ['notMatches', 'two', null];
        // $list['startsWith'] = ['startsWith', 'two', null];

        return $list;
    }

    /**
     * @test
     * @dataProvider assertionsValidProvider
     */
    public function assertionValid(string $assertion, string $fieldValue, mixed $comparison): void
    {
        $validator = $this->makeValidator($fieldValue);

        $validator->assert('nome')->{$assertion}($comparison);

        // se a asserção não passar, uma exceção será lançada
        $validator->validOrResponse();

        $this->assertTrue(true);
    }

    /** @return array<string,array<int,mixed>> */
    public function assertionsInvalidProvider(): array
    {
        $list = [];

        $list['equalTo'] = ['equalTo', ['one', 'onex']];

        return $list;
    }

    // /**
    //  * @test
    //  * @dataProvider assertionsInvalidProvider
    //  */
    // public function assertionInvalid(string $assertion, array $arguments): void
    // {
    //     $this->expectException(AssertionResponseException::class);
        
    //     $validator = $this->makeValidator();

    //     $validator->assert('nome')->{$assertion}(... $arguments);

    //     $validator->validOrResponse();
        
    // }

    // /**
    //  * @test
    //  * @SuppressWarnings(PHPMD.StaticAccess)
    //  */
    // public function assertEqualTo(): void
    // {
    //     // $application = Application::instance();
    //     // $application
    //     //     ->container()
    //     //     ->addFactory('dependency', 'teste');

    //     $object = new class {
    //         use Validable;

    //         public function param(): string
    //         {
    //             return 'field_name';
    //         }
    //     };

    //     $this->assertTrue($object->equalTo('one', 'one'));
    // }
}
