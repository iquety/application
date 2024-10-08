<?php

declare(strict_types=1);

namespace Iquety\Application\Adapter\Session;

use Iquety\Application\Http\Session;
use RuntimeException;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session as SymfonyObject;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Para usar esse adaptador, é preciso instalar a seguinte biblioteca:
 * symfony/http-foundation
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class NativeSession implements Session
{
    private ?SymfonyObject $session = null;

    private bool $testMode = false;

    private function sessionObject(): SymfonyObject
    {
        if ($this->session === null) {
            $storage = $this->testMode === true
                ? new MockArraySessionStorage()
                : new NativeSessionStorage(); // @codeCoverageIgnore

            $this->session = new SymfonyObject($storage, new AttributeBag());
        }

        return $this->session;
    }

    public function enableTestMode(): void
    {
        $this->testMode = true;
    }

    /**
     * Inicia uma sessão.
     * @throws RuntimeException se a inicialização falhar
     */
    public function start(string $identity = ''): void
    {
        $session = $this->sessionObject();
        $session->setId($identity);
        $session->start();
    }

    /** Verifica se a sessão já foi iniciada */
    public function isStarted(): bool
    {
        return $this->sessionObject()->isStarted();
    }

    /** Devolve o ID da sessão */
    public function identity(): string
    {
        return $this->sessionObject()->getId();
    }

    /** Define o nome da sessão */
    public function setName(string $name): void
    {
        $this->sessionObject()->setName($name);
    }

    /** Devolve o nome da sessão */
    public function name(): string
    {
        return $this->sessionObject()->getName();
    }

    /** Define um atributo */
    public function setParam(string $name, mixed $value): void
    {
        $this->sessionObject()->set($name, $value);
    }

    /** Devolve o valor de um atributo */
    public function param(string $name, mixed $default = null): mixed
    {
        return $this->sessionObject()->get($name, $default);
    }

    /** Verifica se o atributo já foi definido */
    public function has(string $name): bool
    {
        return $this->sessionObject()->has($name);
    }

    /** Devolve todos os atributos da sessão */
    public function all(): array
    {
        return $this->sessionObject()->all();
    }

    /** Define vários atributos */
    public function replace(array $attributes): void
    {
        $this->sessionObject()->replace($attributes);
    }

    /** Remove um atributo */
    public function remove(string $name): void
    {
        $this->sessionObject()->remove($name);
    }

    /** Devolve o valor e remove o atributo ao mesmo tempo */
    public function forget(string $name): mixed
    {
        $value = $this->param($name);

        $this->remove($name);

        return $value;
    }

    /** Limpa todos os atributos da sessão */
    public function clear(): void
    {
        $this->sessionObject()->clear();
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
        $this->sessionObject()->invalidate($lifetime);
    }
}
