<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;
use stdClass;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class IsIpTest extends AssertionCase
{
    use HasProviderFieldNotExist;

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $httpParams = [
            'ipv4'             => '192.168.1.1',
            'ipv4_loopback'    => '127.0.0.1',
            'ipv4_broadcast'   => '255.255.255.255',
            'ipv6'             => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'ipv6_compressed'  => '2001:db8:85a3::8a2e:370:7334',
            'ipv6_loopback'    => '::1',
            'ipv6_unspecified' => '::',
        ];

        $list = [];

        foreach (array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $httpParams = [
            'letters'                      => 'abc.def.ghi.jkl',
            'too_many_octets'              => '192.168.1.1.1',
            'missing_octets'               => '192.168.1',
            'out_of_range_octet'           => '256.256.256.256',
            'negative_octet'               => '-1.0.0.0',
            'ipv6_with_invalid_characters' => '2001:db8:85a3::8a2e:370g:7334',
            'ipv6_too_short'               => '2001:db8:85a3',
            'ipv6_too_long'                => '2001:0db8:85a3:0000:0000:8a2e:0370:7334:1234',
            'spaces'                       => '192. 168.1.1',
            'empty_string'                 => '',
            'one_space_string'             => ' ',
            'two_spaces_string'            => '  ',
            'array'                        => ['a']
        ];

        $list = [];

        foreach (array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider validProvider
     * @param array<string,array<int,mixed>> $httpParams
     */
    public function valueAsserted(string $paramName, array $httpParams): void
    {
        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($httpParams),
        );

        $input->assert($paramName)->isIp();

        // se a asserção não passar, uma exceção será lançada
        $input->validOrResponse();

        // se chegar até aqui... tudo correu bem
        $this->assertTrue(true);
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @test
     * @dataProvider invalidProvider
     * @param array<string,array<int,mixed>> $httpParams
     */
    public function valueNotAsserted(string $paramName, array $httpParams): void
    {
        $this->expectException(AssertionResponseException::class);
        $this->expectExceptionMessage('The value was not successfully asserted');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($httpParams),
        );

        $input->assert($paramName)->isIp();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }

    /**
     * @test
     * @dataProvider invalidFieldExistsProvider
     */
    public function fieldDoesNotExist(string $paramName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Field '$paramName' does not exist");

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query(['param_null' => null]),
        );

        $input->assert($paramName)->isIp();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
