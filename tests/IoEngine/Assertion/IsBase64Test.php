<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class IsBase64Test extends AssertionCase
{
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('isBase64');
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $this->setUpProvider();

        $this->setAssertionHttpParams([
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
        ]);

        $list = [];
        
        $list['Base64 Text 1']  = $this->makeAssertionItem('param_base64_text_1');
        $list['Base64 Text 2']  = $this->makeAssertionItem('param_base64_text_2');
        $list['Base64 Text 3']  = $this->makeAssertionItem('param_base64_text_3');
        $list['Base64 Text 4']  = $this->makeAssertionItem('param_base64_text_4');
        $list['Base64 Text 5']  = $this->makeAssertionItem('param_base64_text_5');
        $list['Base64 Text 6']  = $this->makeAssertionItem('param_base64_text_6');
        $list['Base64 Text 7']  = $this->makeAssertionItem('param_base64_text_7');
        $list['Base64 Text 8']  = $this->makeAssertionItem('param_base64_text_8');
        $list['Base64 Text 9']  = $this->makeAssertionItem('param_base64_text_9');
        $list['Base64 Text 10'] = $this->makeAssertionItem('param_base64_text_10');

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $this->setAssertionHttpParams([
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
        ]);

        $list = [];
        
        $list['Not Base64 Text 1'] = $this->makeAssertionItem('param_not_base64_text_1');
        $list['Not Base64 Text 2'] = $this->makeAssertionItem('param_not_base64_text_2');
        $list['Not Base64 Text 3'] = $this->makeAssertionItem('param_not_base64_text_3');
        $list['Not Base64 Text 4'] = $this->makeAssertionItem('param_not_base64_text_4');
        $list['Not Base64 Text 5'] = $this->makeAssertionItem('param_not_base64_text_5');
        $list['Not Base64 Text 6'] = $this->makeAssertionItem('param_not_base64_text_6');
        $list['Not Base64 Text 7'] = $this->makeAssertionItem('param_not_base64_text_7');
        $list['Not Base64 Text 8']  = $this->makeAssertionItem('param_not_base64_text_8');
        $list['Not Base64 Text 9']  = $this->makeAssertionItem('param_not_base64_text_9');
        $list['Not Base64 Text 10'] = $this->makeAssertionItem('param_not_base64_text_10');
        $list['Not Base64 Text 11'] = $this->makeAssertionItem('param_not_base64_text_11');
        $list['Not Base64 Text 12'] = $this->makeAssertionItem('param_not_base64_text_12');
        $list['Not Base64 Text 13'] = $this->makeAssertionItem('param_not_base64_text_13');
        $list['Not Base64 Text 14'] = $this->makeAssertionItem('param_not_base64_text_14');
        $list['Not Base64 Text 15'] = $this->makeAssertionItem('param_not_base64_text_15');
        $list['Not Base64 Text 16'] = $this->makeAssertionItem('param_not_base64_text_16');
        $list['empty string']       = $this->makeAssertionItem('param_empty_string');
        $list['one space string']   = $this->makeAssertionItem('param_one_space_string');
        $list['two spaces string']  = $this->makeAssertionItem('param_two_spaces_string');
        $list['array']              = $this->makeAssertionItem('param_array');
        $list['false']              = $this->makeAssertionItem('param_false');
        // $list['true']               = $this->makeAssertionItem('param_true'); base64_decode(1) curiosamente é igual a ''

        return $list;
    }
}
