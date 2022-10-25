<?php

declare(strict_types=1);

namespace Freep\Application;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class InOut
{
    private int $outputStatus = 200;

    private array $outputs = [];

    public function __construct(
        private RequestInterface $request,
        private ResponseInterface $response
    ) {
    }

    public function input(string $param): string
    {
        // $this->request->getUri()->getQuery()

        if ($this->request->getUri()->getQuery()) {

        }

        return '';
    }

    public function inputMethod(): string
    {
        return mb_strtoupper($this->request->getMethod());
    }

    public function inputPath(): string
    {
        return $this->request->getUri()->getPath();
    }

    public function output(array|string $content, array $headers = []): void
    {
        $this->outputs[] = [
            'headers' => $headers,
            'content' => $content
        ];
    }

    public function setOutputStatus(int $httpStatus): void
    {
        $this->outputStatus = $httpStatus;
    }

    public function outputStatus(): int
    {
        return $this->outputStatus;
    }

    public function emitHeaders(): void
    {
        if ($this->headersEmission === false) {
            return;
        }

        // @codeCoverageIgnoreStart
        foreach ($this->getOutputHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        // @codeCoverageIgnoreEnd
    }
    
    public function getOutputHeaders(): array
    {
        $headers = [];

        foreach($this->outputs as $headers) {
            foreach ($headers as $name => $value) {
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    public function getOutputAsJson(): string
    {
        $content = [];

        foreach($this->outputs as $data) {
            $content[] = $data['content'];
        }

        return (string)json_encode($content, JSON_FORCE_OBJECT);
    }

    public function getOutputAsMarkup(): string
    {
        $content = [];

        foreach($this->outputs as $data) {
            $content .= $data['content'];
        }

        return $content;
    }

    public function getOutputAsBlob(): string
    {
        $content = [];

        foreach($this->outputs as $data) {
            $content .= $data['content'];
        }

        return $content;
    }
}
