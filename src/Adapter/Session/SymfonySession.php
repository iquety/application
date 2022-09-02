<?php

declare(strict_types=1);

namespace Freep\Application\Adapter\Session;

use Freep\Application\Http\Session;
use Symfony\Component\HttpFoundation\Session\Session as SymfonyObject;

/**
 * Para usar esse adaptador, é preciso instalar a seguinte biblioteca:
 * symfony/http-foundation
 */
class SymfonySession implements Session
{
    private ?SymfonyObject $session = null;

        /** Limpa todos os atributos da sessão */
    public function clear(): void
    {
        $this->session = null;
    }

    /** Verifica se o atributo já foi definido */
    public function has(string $name): bool
    {
        return $this->session->has($name);
    }

    /** Devolve o ID da sessão */
    public function identity(): string
    {
        return $this->session->getId();
    }
    
    /**
     * Limpa os atributos da memória e regenera a sessão.
     * Se existirem atributos pesistidos, remove-os também.
     * @param int $lifetime tempo de vida do cookie em segundos
     * A null value will leave the system settings unchanged, 
     * 0 sets the cookie to expire with browser session. 
     * Time is in seconds, and is not a Unix timestamp.
     */
    public function invalidate(int $lifetime = null): void
    {
        $this->session->invalidate($lifetime);
    }

    /** Verifica se a sessão já foi iniciada */
    public function isStarted(): bool
    {
        return $this->session->isStarted();
    }

    /** Devolve o nome da sessão */
    public function name(): string
    {
        return $this->session->getName();
    }

    /** Define o ID da sessão */
    public function setIdentity(string $id)
    {
        return $this->session->setId($id);
    }

    /** Define o nome da sessão */
    public function setName(string $name)
    {
        return $this->session->setName($name);
    }

    /**
     * Inicia uma sessão.
     * @throws RuntimeException se a inicialização falhar
     */
    public function start(): bool
    {
        return $this->session->start();
    }
    
    /** Devolve o valor e remove o atributo ao mesmo tempo */
    public function forget(string $name): mixed
    {
        $value = $this->param($name);

        $this->remove($name);

        return $value;
    }

    /** Devolve o valor de um atributo */
    public function param(string $name, mixed $default = null): mixed
    {
        return $this->session->get($name, $default);
    }

    /** Define um atributo */
    public function setParam(string $name, mixed $value): void
    {
        $this->session->set($name, $value);
    }

    /** Devolve todos os atributos da sessão */
    public function all(): array
    {
        return $this->session->all();
    }

    /** Define vários atributos */
    public function replace(array $attributes): void
    {
        $this->session->replace($attributes);
    }

    /** Remove um atributo */
    public function remove(string $name): void
    {
        $this->session->remove($name);
    }
}
