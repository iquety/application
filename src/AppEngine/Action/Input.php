<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Action;

use InvalidArgumentException;
use Iquety\Application\AppEngine\File;
use Iquety\Application\AppEngine\FileSet;
use Iquety\Application\AppEngine\UriParser;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

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

    /** @var array<int,float|int|string> */
    private array $target = [];

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
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );

        return new self($parser->getPath(), $paramList, $request->getMethod());
    }

    /**
     * @param array<int,string> $path 
     * @param array<int|string,float|int|string|array<string,int|string>> $originalParamList
     */
    private function __construct(array $originalPath, array $originalParamList, string $method)
    {
        $this->method = mb_strtoupper($method);

        $this->path = $originalPath;
        
        foreach($originalParamList as $name => $value) {
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

    public function getPath(): array
    {
        return $this->path;
    }

    public function getPathString(): string
    {
        return implode('/', $this->path);
    }

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

    /** @return array<int|string,Param> */
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

    private function makeFileSet(array $fileList): FileSet
    {
        $fileSet = new FileSet();

        // validar as informações dos arquivos
        foreach($fileList as $uploadedFile) {
            if (! $uploadedFile instanceof UploadedFileInterface) {
                throw new InvalidArgumentException('File structure is invalid');
            }

            $fileSet->add(new File($uploadedFile));
        }

        return $fileSet;
    }
}
