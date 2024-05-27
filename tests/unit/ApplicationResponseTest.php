<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpMime;
use Iquety\Application\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationResponseTest extends ApplicationCase
{
    public function makeResponseProvider(HttpMethod $httpMethod): array
    {
        $list = [];

        // HTML

        $list[$httpMethod->value . ' array to html'] = [
            $httpMethod,
            HttpMime::HTML,
            '/mvc-array/22',
            'Error: An HTML response must be textual content',
            HttpStatus::INTERNAL_SERVER_ERROR,
        ];

        $list[$httpMethod->value . ' association to html'] = [
            $httpMethod,
            HttpMime::HTML,
            '/mvc-associative/22',
            'Error: An HTML response must be textual content',
            HttpStatus::INTERNAL_SERVER_ERROR,
        ];

        $list[$httpMethod->value . ' response to html'] = [
            $httpMethod,
            HttpMime::HTML,
            '/mvc-response/22',
            'Resposta com base em ResponseInterface para id 22 input 0=22&id=22',
            HttpStatus::OK
        ];

        $list[$httpMethod->value . ' string to html'] = [
            $httpMethod,
            HttpMime::HTML,
            '/mvc-string/22',
            'Resposta com base em texto para id 22 input 0=22&id=22',
            HttpStatus::OK
        ];

        // JSON

        $json = '{' 
              . '"0":"Resposta com base em array",'
              . '"1":"Id 22",'
              . '"2":"Input 0=22&id=22"'
              . '}';

        $list[$httpMethod->value . ' array to json'] = [
            $httpMethod,
            HttpMime::JSON,
            '/mvc-array/22',
            $json,
            HttpStatus::OK
        ];

        $json = '{' 
            . '"message":"Resposta com base em array",'
            . '"id":22,'
            . '"input":"0=22&id=22"'
            . '}';

        $list[$httpMethod->value . ' association to json'] = [
            $httpMethod,
            HttpMime::JSON,
            '/mvc-associative/22',
            $json,
            HttpStatus::OK
        ];

        $list[$httpMethod->value . ' response to json'] = [
            $httpMethod,
            HttpMime::JSON,
            '/mvc-response/22',
            'Resposta com base em ResponseInterface para id 22 input 0=22&id=22',
            HttpStatus::OK
        ];

        $list[$httpMethod->value . ' string to json'] = [
            $httpMethod,
            HttpMime::JSON,
            '/mvc-string/22',
            '{"content":"Resposta com base em texto para id 22 input 0=22&id=22"}',
            HttpStatus::OK
        ];

        // XML

        $xml = "<?xml version=\"1.0\"?>\n" 
             . "<root>"
             . "<item>Resposta com base em array</item>"
             . "<item>Id 22</item>"
             . "<item>Input 0=22&amp;id=22</item>"
             . "</root>\n";
             
        $list[$httpMethod->value . ' array to xml'] = [
            $httpMethod,
            HttpMime::XML,
            '/mvc-array/22',
            $xml,
            HttpStatus::OK
        ];

        $xml = "<?xml version=\"1.0\"?>\n" 
             . "<root>"
             . "<message>Resposta com base em array</message>"
             . "<id>22</id>"
             . "<input>0=22&amp;id=22</input>"
             . "</root>\n";

        $list[$httpMethod->value . ' association to xml'] = [
            $httpMethod,
            HttpMime::XML,
            '/mvc-associative/22',
            $xml,
            HttpStatus::OK
        ];

        $list[$httpMethod->value . ' response to xml'] = [
            $httpMethod,
            HttpMime::XML,
            '/mvc-response/22',
            'Resposta com base em ResponseInterface para id 22 input 0=22&id=22',
            HttpStatus::OK
        ];

        $xml = "<?xml version=\"1.0\"?>\n" 
             . "<root>"
             . "<content>Resposta com base em texto para id 22 input 0=22&amp;id=22</content>"
             . "</root>\n";

        $list[$httpMethod->value . ' string to xml'] = [
            $httpMethod,
            HttpMime::XML,
            '/mvc-string/22',
            $xml,
            HttpStatus::OK
        ];

        // TEXT

        $list[$httpMethod->value . ' array to text'] = [
            $httpMethod,
            HttpMime::TEXT,
            '/mvc-array/22',
            'Error: An text response must be textual content',
            HttpStatus::INTERNAL_SERVER_ERROR,
        ];

        $list[$httpMethod->value . ' association to text'] = [
            $httpMethod,
            HttpMime::TEXT,
            '/mvc-associative/22',
            'Error: An text response must be textual content',
            HttpStatus::INTERNAL_SERVER_ERROR,
        ];

        $list[$httpMethod->value . ' response to text'] = [
            $httpMethod,
            HttpMime::TEXT,
            '/mvc-response/22',
            'Resposta com base em ResponseInterface para id 22 input 0=22&id=22',
            HttpStatus::OK
        ];

        $list[$httpMethod->value . ' string to text'] = [
            $httpMethod,
            HttpMime::TEXT,
            '/mvc-string/22',
            'Resposta com base em texto para id 22 input 0=22&id=22',
            HttpStatus::OK
        ];

        return $list;
    }

    public function responseProvider(): array
    {
        return array_merge(
            [],
            $this->makeResponseProvider(HttpMethod::ANY),
            $this->makeResponseProvider(HttpMethod::GET),
            $this->makeResponseProvider(HttpMethod::POST),
            $this->makeResponseProvider(HttpMethod::PUT),
            $this->makeResponseProvider(HttpMethod::DELETE),
        );
    }

    /**
     * @test 
     * @dataProvider responseProvider
     */
    public function runArray(
        HttpMethod $httpMethod,
        HttpMime $acceptMimeType,
        string $uri,
        string $responseBody,
        HttpStatus $httpStatus
    ): void {
        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest($uri, $httpMethod, $acceptMimeType)
        );

        $instance->bootEngine(new MvcEngine());

        $instance->bootApplication($this->makeMvcBootstrapResponses());

        $response = $instance->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString($responseBody, (string)$response->getBody());
        $this->assertSame($httpStatus->value, $response->getStatusCode());
    }
}
