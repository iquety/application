<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class IsUrlTest extends AssertionCase
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
            'http'          => 'http://www.example.com',
            'https'         => 'https://www.example.com',
            'with_path'     => 'http://www.example.com/path',
            'with_query'    => 'http://www.example.com/path?query=123',
            'with_fragment' => 'http://www.example.com/path#fragment',
            'ip_address'    => 'http://192.168.1.1',
            'localhost'     => 'http://localhost',
            'subdomain'     => 'http://subdomain.example.com',
            'port'          => 'http://www.example.com:8080',
            'long_tld'      => 'http://www.example.museum',
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
            'invalid_url_no_scheme' => 'www.example.com',
            'invalid_url_no_domain' => 'http://',
            'invalid_url_spaces'    => 'http://example .com',
            // 'invalid_url_special_characters' => 'http://example.com/<>',
            // 'invalid_url_incomplete_tld'     => 'http://example.c',
            // 'invalid_url_no_tld'             => 'http://example',
            'invalid_url_ip_without_scheme' => '192.168.1.1',
            'invalid_url_missing_slashes'   => 'http:example.com',
            'invalid_url_double_dots'       => 'http://example..com',
            'invalid_url_empty_string'      => '',
            'invalid_url_chars_1'           => 'http://&example.com/捦挺挎/bar',
            'invalid_url_chars_2'           =>
                'www.hti.umich.edu/cgi/t/text/pageviewer-idx'
                . '?c=umhistmath;cc=umhistmath;rgn=full%20text;'
                . 'idno=ABS3153.0001.001;didno=ABS3153.0001.001;view=image;seq=00000140',
            'integer' => 1234
            // 'empty_array' => []
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

        $input->assert($paramName)->isUrl();

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

        $input->assert($paramName)->isUrl();

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

        $input->assert($paramName)->isUrl();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
