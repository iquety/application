<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use InvalidArgumentException;
use PhpParser\Node\Expr\Instanceof_;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class Input
{
    /** @var array<int|string,float|int|string|FileSet> */
    private array $paramList = [];

    /** @var array<int|string,float|int|string|FileSet> */
    private array $originalParamList = [];

    public static function fromString(string $string): self
    {
        return new self((new UriParser($string))->toArray());
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $paramList = (new UriParser($request->getRequestTarget()))->toArray();

        $paramList = array_merge(
            $paramList,
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );

        return new self($paramList);
    }

    public static function fromInput(Input $input): self
    {
        $paramList = array_map(fn($item) => $item->value(), $input->toArray());

        return new self($paramList);
    }

    /** @param array<int|string,float|int|string|array<string,int|string>> $originalParamList */
    private function __construct(array $originalParamList)
    {
        foreach($originalParamList as $name => $value) {
            if ($value instanceof UploadedFileInterface) {
                $this->originalParamList[$name] = $this->makeFileSet([ $value ]);

                continue;
            }

            if (is_array($value) === true) {
                $this->originalParamList[$name] = $this->makeFileSet($value);

                continue;
            }

            $this->originalParamList[$name] = $value;
        }

        $this->paramList = $originalParamList;
    }

    public function next(): void
    {
        array_pop($this->paramList);
    }

    public function reset(): void
    {
        $this->paramList = $this->originalParamList;
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
        return http_build_query(
            array_map(fn($item) => $item, $this->paramList)
        );
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
