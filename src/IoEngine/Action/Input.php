<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use InvalidArgumentException;
use Iquety\Application\IoEngine\File;
use Iquety\Application\IoEngine\FileSet;
use Iquety\Application\IoEngine\UriParser;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/** Contém os parâmetros de entrada do usuário */
class Input
{
    private bool $hasNext;

    private string $method = 'GET';

    /** @var array<int|string,float|int|string|FileSet> */
    private array $paramList = [];

    /** @var array<int,string> */
    private array $path = [];

    /** @var array<int|string,float|int|string|FileSet> */
    private array $originalParamList = [];

    /** @var array<int,string> */
    private array $target = [];

    public static function fromConsoleArguments(array $argumentList): self
    {
        if (isset($argumentList[0]) === false || trim($argumentList[0]) === '') {
            throw new RuntimeException(
                'The argument list is corrupt. It does not contain the script name'
            );
        }

        return new self([], $argumentList, 'CLI');
    }

    public static function fromString(string $string): self
    {
        $parser = new UriParser($string);

        return new self($parser->getPath(), $parser->toArray(), 'GET');
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $parser = new UriParser($request->getRequestTarget());

        $paramList = array_merge(
            $parser->toArray(),
            (array)$request->getParsedBody(),
            $request->getUploadedFiles()
        );

        return new self($parser->getPath(), $paramList, $request->getMethod());
    }

    /**
     * @param array<int,string> $originalPath
     * @param array<int|string,mixed> $originalParamList
     */
    private function __construct(array $originalPath, array $originalParamList, string $method)
    {
        $this->method = mb_strtoupper($method);

        $this->path = $originalPath;

        foreach ($originalParamList as $name => $value) {
            if ($value instanceof UploadedFileInterface) {
                $this->paramList[$name] = $this->makeFileSet([ $value ]);

                continue;
            }

            if (is_array($value) === true) {
                $this->paramList[$name] = $this->makeFileSet($value);

                continue;
            }

            $this->paramList[$name] = $value;
        }

        $this->originalParamList = $this->paramList;

        $this->reset();
    }

    /** @param array<int|string,mixed> $paramList */
    public function appendParams(array $paramList): void
    {
        $this->originalParamList = array_merge(
            $this->originalParamList,
            $paramList
        );

        $this->reset();
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /** @return array<int,string> */
    public function getPath(): array
    {
        return $this->path;
    }

    public function getPathString(): string
    {
        return implode('/', $this->path);
    }

    /** @return array<int,string> */
    public function getTarget(): array
    {
        return $this->target;
    }

    public function hasNext(): bool
    {
        return $this->hasNext;
    }

    public function next(): void
    {
        if ($this->hasNext === false) {
            return;
        }

        $currentKey = count($this->target);

        $this->target[] = $this->path[$currentKey];

        array_shift($this->paramList);

        if (count($this->target) === count($this->path)) {
            $this->hasNext = false;
        }
    }

    public function reset(): void
    {
        $this->hasNext = count($this->path) > 0
            ? true
            : false;

        $this->paramList = $this->originalParamList;

        $this->target = [];

        $this->next();
    }

    public function param(int|string $param): float|int|string|FileSet|null
    {
        return $this->paramList[$param] ?? null;
    }

    /** @return array<int|string,mixed> */
    public function toArray(): array
    {
        return $this->paramList;
    }

    public function __toString(): string
    {
        return http_build_query(array_map(
            fn($item) => (string)$item,
            $this->paramList
        ));
    }

    /** @param array<int,UploadedFileInterface> $fileList */
    private function makeFileSet(array $fileList): FileSet
    {
        $fileSet = new FileSet();

        // validar as informações dos arquivos
        foreach ($fileList as $uploadedFile) {
            if (! $uploadedFile instanceof UploadedFileInterface) {
                throw new InvalidArgumentException('File structure is invalid');
            }

            $fileSet->add(new File($uploadedFile));
        }

        return $fileSet;
    }
}
