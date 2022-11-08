<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\CommandPossibility;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HandlerPossibilitiesTest extends TestCase
{
    public function addPossibility(
        array & $list,
        string $path,
        array $commandPossibilities
    ): void {

        $possibilityList = [];

        foreach ($commandPossibilities as $commandSufix => $params) {
            $possibilityList[] = new CommandPossibility(
                FcBootstrapConcrete::class,
                $this->extractNamespace(FcBootstrapConcrete::class, $commandSufix),
                $params
            );
        }

        $list[$path] = [$path, $possibilityList];
    }
    
    public function possibilitiesProvider(): array
    {
        $list = [];

        $this->addPossibility($list, 'one', [
            'CommandsDir\One' => [],
        ]);

        $this->addPossibility($list, 'one/two', [
            'CommandsDir\OneTwo' => [],
            'CommandsDir\One' => ['two'],
            'CommandsDir\One\Two' => [],
        ]);

        $this->addPossibility($list, 'one/two/three', [
            'CommandsDir\OneTwoThree' => [],
            'CommandsDir\OneTwo' => ['three'],
            'CommandsDir\One' => ['two', 'three'],
            'CommandsDir\One\TwoThree' => [],
            'CommandsDir\One\Two' => ['three'],
        ]);

        $this->addPossibility($list, 'alpha/beta', [
            'CommandsDir\AlphaBeta' => [],
            'CommandsDir\Alpha' => ['beta'],
            'CommandsDir\Alpha\Beta' => [],
        ]);

        $this->addPossibility($list, 'alpha/beta/gamma', [
            'CommandsDir\AlphaBetaGamma' => [],
            'CommandsDir\AlphaBeta' => ['gamma'],
            'CommandsDir\Alpha' => ['beta', 'gamma'],
            'CommandsDir\Alpha\BetaGamma' => [],
            'CommandsDir\Alpha\Beta' => ['gamma'],
        ]);

        $this->addPossibility($list, 'alpha/beta/gamma/delta', [
            'CommandsDir\AlphaBetaGammaDelta' => [],
            'CommandsDir\AlphaBetaGamma' => ['delta'],
            'CommandsDir\AlphaBeta' => ['gamma', 'delta'],
            'CommandsDir\Alpha' => ['beta', 'gamma', 'delta'],
            'CommandsDir\Alpha\BetaGammaDelta' => [],
            'CommandsDir\Alpha\BetaGamma' => ['delta'],
            'CommandsDir\Alpha\Beta' => ['gamma', 'delta'],
        ]);

        $this->addPossibility($list, 'alpha/beta/gamma/delta/33', [
            'CommandsDir\AlphaBetaGammaDelta33' => [],
            'CommandsDir\AlphaBetaGammaDelta' => [33],
            'CommandsDir\AlphaBetaGamma' => ['delta', 33],
            'CommandsDir\AlphaBeta' => ['gamma', 'delta', 33],
            'CommandsDir\Alpha' => ['beta', 'gamma', 'delta', 33],
            'CommandsDir\Alpha\BetaGammaDelta33' => [],
            'CommandsDir\Alpha\BetaGammaDelta' => [33],
            'CommandsDir\Alpha\BetaGamma' => ['delta', 33],
            'CommandsDir\Alpha\Beta' => ['gamma', 'delta', 33],
        ]);

        $this->addPossibility($list, 'alpha/beta/gamma/delta/33/upsilon', [
            'CommandsDir\AlphaBetaGammaDelta33Upsilon' => [],
            'CommandsDir\AlphaBetaGammaDelta33' => ['upsilon'],
            'CommandsDir\AlphaBetaGammaDelta' => [33, 'upsilon'],
            'CommandsDir\AlphaBetaGamma' => ['delta', 33, 'upsilon'],
            'CommandsDir\AlphaBeta' => ['gamma', 'delta', 33, 'upsilon'],
            'CommandsDir\Alpha' => ['beta', 'gamma', 'delta', 33, 'upsilon'],
            'CommandsDir\Alpha\BetaGammaDelta33Upsilon' => [],
            'CommandsDir\Alpha\BetaGammaDelta33' => ['upsilon'],
            'CommandsDir\Alpha\BetaGammaDelta' => [33, 'upsilon'],
            'CommandsDir\Alpha\BetaGamma' => ['delta', 33, 'upsilon'],
            'CommandsDir\Alpha\Beta' => ['gamma', 'delta', 33, 'upsilon'],
        ]);

        $this->addPossibility($list, 'alpha/beta/gamma/delta/33/4.5', [
            'CommandsDir\AlphaBetaGammaDelta334.5' => [],
            'CommandsDir\AlphaBetaGammaDelta33' => [4.5],
            'CommandsDir\AlphaBetaGammaDelta' => [33, 4.5],
            'CommandsDir\AlphaBetaGamma' => ['delta', 33, 4.5],
            'CommandsDir\AlphaBeta' => ['gamma', 'delta', 33, 4.5],
            'CommandsDir\Alpha' => ['beta', 'gamma', 'delta', 33, 4.5],
            'CommandsDir\Alpha\BetaGammaDelta334.5' => [],
            'CommandsDir\Alpha\BetaGammaDelta33' => [4.5],
            'CommandsDir\Alpha\BetaGammaDelta' => [33, 4.5],
            'CommandsDir\Alpha\BetaGamma' => ['delta', 33, 4.5],
            'CommandsDir\Alpha\Beta' => ['gamma', 'delta', 33, 4.5],
        ]);

        return $list;
    }

    /**
     * @test
     * @dataProvider possibilitiesProvider
     */
    public function possiblitities(
        string $path,
        array $commandPossibilities
    ): void {
        $handler = new CommandHandler();
        
        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $amount = count($commandPossibilities);

        $resultPossibilities = $handler->process($path);

        // certifica que o teste está cobrindo todas as possibilidades
        $this->assertCount($amount, $resultPossibilities);

        for($index = 0; $index < $amount; $index++) {
            /** @var CommandPossibility */
            $processedPossibility = $resultPossibilities[$index];
            $testPossibility = $commandPossibilities[$index];

            $this->assertSame($processedPossibility->callable(), $testPossibility->callable());
            $this->assertSame($processedPossibility->params(), $testPossibility->params());
        }
    }
}
