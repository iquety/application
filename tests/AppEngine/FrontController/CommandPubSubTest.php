<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use DateTimeImmutable;
use Exception;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Action\MethodNotAllowedException;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\Mvc\Stubs\AnyController;
use Tests\AppEngine\Mvc\Stubs\CheckMethodController;
use Tests\AppEngine\Mvc\Stubs\DeleteController;
use Tests\AppEngine\Mvc\Stubs\GetController;
use Tests\AppEngine\Mvc\Stubs\PatchController;
use Tests\AppEngine\Mvc\Stubs\PostController;
use Tests\AppEngine\Mvc\Stubs\PutController;
use Tests\PubSub\FakeEventOccurred;
use Tests\PubSub\FakeSubscriber;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class CommandPubSubTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function publish(): void
    {
        $application = Application::instance();

        $application->bootEventPublisher(SimpleEventPublisher::instance());

        $application->addSubscriber('channel-one', FakeSubscriber::class);

        $command = new class extends Command {
            public function execute(): void
            {
                $this->publish(
                    'channel-one',
                    new FakeEventOccurred('Title', 'Description', new DateTimeImmutable())
                );
            }
        };

        // ao executar a action, o evento FakeEventOccurred é publicado e o
        // assinante FakeSubscriber manipula-o com o método handleEvent, que
        // simplesmente printa na tela a identificação dele
        ob_start();
        $command->execute();
        $output = ob_get_clean();

        $this->assertSame('Event post.register.v1 occurred', $output);
    }
}
