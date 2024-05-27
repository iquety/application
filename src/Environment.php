<?php

declare(strict_types=1);

namespace Iquety\Application;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
enum Environment: string
{
    /** 
     * Usado pelos desenvolvedores, para a versão instável do sofware.
     * - Criatividade flui livremente neste ambiente;
     * - Iteração rápida, feedbacks e aprendizado com os erros;
     * - Comunicação e colaboração
     */
    case DEVELOPMENT = 'development';

    /**
     * Usado pelo usuário final, para a versão estável do sofware
     * em uso no dia a dia.
     * - Confiabilidade, estabilidade e desempenho otimizado;
     * - Satisfação do usuário;
     * - Monitoramento contínuo;
     * - Escalabilidade para diferentes cargas de usuários.
     */
    case PRODUCTION = 'production';

    /**
     * Após a fase de desenvolvimento atingir um ponto estável,
     * o software entre em modo de "preparação".
     * Este ambiente possui uma configuração mais próxima
     * possível do ambiente de produção, para um exame 
     * minucioso do comportamento do software em um 
     * ambiente controlado. 
     * - Testes completos e rigorosos simulam condições do mundo real;
     * - Garantia de qualidade conforme as expectativas dos usuários;
     * - Controle de Versão para ajudar a rastrear as alterações.
     */ 
    case STAGE = 'stage';

    /**
     * Usado na execução dos testes automatizados.
     * - Testes de unidade;
     * - Testes de integração;
     * - Testes E2E
     * - Testes comportamentais
     */
    case TESTING = 'testing';

    public static function makeBy(string $enviromnent): Environment
    {
        return match ($enviromnent) {
            'development' => self::DEVELOPMENT,
            'production'  => self::PRODUCTION,
            'stage'       => self::STAGE,
            'testing'     => self::TESTING,
            default       => self::PRODUCTION
        };
    }
}
