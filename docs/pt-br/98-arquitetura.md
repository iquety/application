# Arquitetura

## Application

A classe Application é a coluna cervical do projeto.

### Construção

A construção da aplicação inicializa os recipientes:

- container: o recipiente global de dependencias
- moduleSet: a lista de módulos registrados
- publisherSet: a lista de publicadores registrados
- engineSet: a lista de motores registrados
- timezone: 'America/Sao_Paulo'

### Método bootApplication e bootModule

Ambos os métodos adicionam módulos à aplicação.

- bootApplication: adiciona o módulo principal
- bootModule: adiciona módulos secundários

### Método bootEngine

Adiciona um motor de interpretação de entrada e saída.
O primeiro motor adicionado será marcado como o principal da aplicação.
Os que forem adicionados em seguida, serão motores secundários.

### Método run

Este método executa a aplicação e devolve uma resposta.

#### Solicitação web

Quando a requisição é proveniente de uma transação HTTP, apenas os motores
compatíveis serão executados:

- FcEngine: o motor para o padrão FrontController
- MvcEngine: o motor para o padrão MVC

**ActionExecutor:** a resolução da URI solicitada é efetuada pelo `ActionExecutor`
que produz um `ActionDescriptor` contendo o resultado da resolução

**ActionDescriptor:** um descritor contendo as informações necessárias para a 
executar a ação (Mvc:Controller ou FrontController:Command) solicitada.

- decide se $actionClasse é do tipo $actionType  
- tenta executar a regra $actionClass::$actionMethod
- devolve a resposta se sucesso
- devolve a resposta notFound do módulo principal caso não exista
- devolve a resposta error do módulo principal caso um erro ocorra

#### Solicitação cli

Quando a requisição é proveniente de um terminal de comandos, todos os motores
serão executados:

- ConsoleEngine: o motor para interpretar comandos de terminal
- FcEngine: o motor para o padrão FrontController
- MvcEngine: o motor para o padrão MVC

A resolução de qual rotina de terminal executar é efetuada pela biblioteca
`iquety/console`.

![Fluxograma](../../docs-src/gherkin/fluxograma.png)
