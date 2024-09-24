<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;

class RequestFactory
{
    /**
     * Transforma a estrutura de características (recebida pelo PHP) em uma
     * estrutura mais correta, de entidades
     *
     * Para envios de upload único (name=inputName), a estrutura do arquivo será
     * mantida.
     *
     * Envios múltiplos (name=inputName[]) serão padronizados em seus próprios
     * índices, como explicado abaixo.
     *
     * Isso:
     *
     * 'htmlInputName' => [
     *     'name' => [
     *         0 => 'attachment.gif',
     *         1 => 'attachment.jpg',
     *     ],
     *     'type' => [
     *          0 => 'image/gif',
     *          1 => 'image/jpeg',
     *     ],
     * ]
     *
     * Se tornará isso:
     *
     * 'htmlInputName' => [
     *     0 => [
     *         'name' => 'attachment.gif',
     *         'type' => 'image/gif'
     *     ],
     *     1 => [
     *         'name' => 'attachment.jpg'
     *         'type' => 'image/jpeg'
     *     ],
     * ]
     * @param array<int|string,mixed> $phpFiles
     * @return array<string,array<string,mixed>|array<int,array<string,mixed>>>
     */
    private function normalizedFiles(array $phpFiles): array
    {
        foreach ($phpFiles as $inputName => $fileList) {
            $phpFiles[$inputName] = $this->normalizedInput($fileList);
        }

        return $phpFiles;
    }

    /**
     * @param array<int|string,mixed> $fileList
     * @return array<int,array<string,mixed>>
     */
    private function normalizedInput(array $fileList): array
    {
        $normalizedList = [];

        $keyList  = ['name', 'full_path', 'type', 'tmp_name', 'error', 'size'];

        foreach ($keyList as $key) {
            // arquivo único tem estrutura diferente
            if (is_array($fileList[$key]) === false) {
                $normalizedList[$key] = $fileList[$key];
                continue;
            }

            foreach ($fileList[$key] as $index => $info) {
                $normalizedList[$index][$key] = $info;
            }
        }

        return $normalizedList;
    }

    /**
     * @param array<string,mixed> $payload
     * @param array<int|string,mixed> $uploadedFiles
     */
    public function makeRequest(
        string $method,
        string $path,
        string $query,
        array $payload,
        array $uploadedFiles
    ): ServerRequestInterface {
        $queryParamList = [];

        parse_str($query, $queryParamList);

        return (new ServerRequestFactory())->fromGlobals(
            [
                'REQUEST_URI'    => "$path?$query",
                'QUERY_STRING'   => $query,
                'REQUEST_METHOD' => mb_strtoupper($method)
            ],
            null,
            $payload,
            null,
            $this->makeUploadedFiles($this->normalizedFiles($uploadedFiles)),
            null
        );
    }

    /**
     * @param array<string,array<string,mixed>|array<int,array<string,mixed>>> $uploadedFiles
     * @return array<string,array<int,UploadedFile>>
     */
    private function makeUploadedFiles(array $uploadedFiles): array
    {
        $structure = [];

        foreach ($uploadedFiles as $inputName => $inputFiles) {
            $structure[$inputName] = $this->makeUploadedInput($inputFiles);
        }

        return $structure;
    }

    /**
     * @param array<string,mixed>|array<int,array<string,mixed>> $fileSet
     * @return array<int,UploadedFile>
     */
    private function makeUploadedInput(array $fileSet): array
    {
        $fileList = [];

        // enviado um único arquivo
        if (isset($fileSet[0]) === false) {
            $file = $fileSet;

            $fileList[] = new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );

            return $fileList;
        }

        foreach ($fileSet as $file) {
            $fileList[] = new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );
        }

        return $fileList;
    }
}
