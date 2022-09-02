<?php

declare(strict_types=1);

namespace Freep\Application\Http;

interface Session
{
    /** Limpa todos os atributos da sessão */
    public function clear(): void;

    /** Verifica se o atributo já foi definido */
    public function has(string $name): bool;

    /** Devolve o ID da sessão */
    public function identity(): string;
    
    /**
     * Limpa os atributos da memória e regenera a sessão.
     * Se existirem atributos pesistidos, remove-os também.
     * @param int $lifetime tempo de vida do cookie em segundos
     * @throws RuntimeException se a invalidação falhar
     */
    public function invalidate(int $lifetime = null): void;

    /** Verifica se a sessão já foi iniciada */
    public function isStarted(): bool;

    /** Devolve o nome da sessão */
    public function name(): string;

    /** Define o ID da sessão */
    public function setIdentity(string $id);

    /** Define o nome da sessão */
    public function setName(string $name);

    /**
     * Inicia uma sessão.
     * @throws RuntimeException se a inicialização falhar
     */
    public function start(): bool;
    
    /** Devolve o valor e remove o atributo ao mesmo tempo */
    public function forget(string $name): mixed;

    /** Devolve o valor de um atributo */
    public function param(string $name, mixed $default = null): mixed;

    /** Define um atributo */
    public function setParam(string $name, mixed $value): void;

    /** Devolve todos os atributos da sessão */
    public function all(): array;

    /** Define vários atributos */
    public function replace(array $attributes): void;

    /** Remove um atributo */
    public function remove(string $name): void;
}
