<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsBase64Test extends AssertionCase
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
            'param_base64_text_1'  => base64_encode('Texto123'),
            'param_base64_text_2'  => base64_encode('abc123'),
            'param_base64_text_3'  => base64_encode('123xyz'),
            'param_base64_text_4'  => base64_encode('TextoABC123'),
            'param_base64_text_5'  => base64_encode('123XYZTexto'),
            'param_base64_text_6'  => base64_encode('Texto123XYZ'),
            'param_base64_text_7'  => base64_encode('TextoABC'),
            'param_base64_text_8'  => base64_encode('abc123xyz'),
            'param_base64_text_9'  => base64_encode('123'),
            'param_base64_text_10' => base64_encode('texto'),
        ];

        $list = [];

        foreach(array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }
        
        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $httpParams = [
            'param_not_base64_text_1' => 'Te&xto123',
            'param_not_base64_text_2' => 'abçc123=',
            'param_not_base64_text_3' => '12á3xyz=',
            'param_not_base64_text_4' => 'Te^xtoABC123=',
            'param_not_base64_text_5' => '123*XYZTexto=',
            'param_not_base64_text_6' => 'Text)o123XYZ = ',
            'param_not_base64_text_7' => 'Tex(toABC=',
            'param_not_base64_text_8'  => 'ab@c123xyz=',
            'param_not_base64_text_9'  => '13#23=',
            'param_not_base64_text_10' => 't$exto=',
            'param_not_base64_text_11' => '%+',
            'param_not_base64_text_12' => '&/',
            'param_not_base64_text_13' => '_=',
            'param_not_base64_text_14' => '&=+==',
            'param_not_base64_text_15' => '&+/=',
            'param_not_base64_text_16' => '&+/==',
            'param_empty_string'       => '',
            'param_one_space_string'   => ' ',
            'param_two_spaces_string'  => '  ',
            'param_array'              => ['a'],
            'param_false'              => false, // false é mudado para 0
            // 'param_true'               => true, // false é mudado para 1
            'param_string_false'       => 'false', // false é mudado para 0
            'param_string_true'        => 'true', // false é mudado para 1
        ];

        $list = [];
        
        foreach(array_keys($httpParams) as $param) {
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

        $input->assert($paramName)->isBase64();

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

        $input->assert($paramName)->isBase64();

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

        $input->assert($paramName)->isBase64();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
