#language: pt
Funcionalidade: Instanciação
Sendo um projeto qualquer
Quero instanciar o objeto Aplicacao
Para que uma solicitação seja processada e uma resposta seja produzida

Cenário: Fabricação de uma Aplicação
    Dado que a biblioteca seja instalada no projeto
    Quando a instanciação for efetuada
    Então um "Container" será instanciado
    E uma "Lista de Módulos" será iniciada
    E uma "Lista de Publicadores de Eventos" será iniciada
    E uma "Lista de Motores" será iniciada
    E a "Zona de Tempo" será determinada para "America/Sao_Paulo"
    E o "Ambiente" será definido como "Development"

Cenário: Registro do módulo principal
    Dado foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootApplication" for invocada
    E um "Bootstrap" for fornecida como argumento
    Então o "Bootstrap" será adicionado à "Lista de Módulos"
    E o "Bootstrap" será definido como "Módulo Principal"

Cenário: Registro de um módulo secundário
    Dado foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootModule" for invocada
    E um "Bootstrap" for fornecida como argumento
    Então o "Bootstrap" será adicionado à "Lista de Módulos"

Cenário: Registro de um Motor de I/O
    Dado foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootEngine" for invocada
    E um "Motor de I/O" for fornecido como argumento
    Então a "Lista de Módulos" será compartilhada com o "Motor de I/O"
    E o "Motor de I/O" será adicionado à "Lista de Motores"

Cenário: Registro de um Publicador de Eventos
    Dado foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootEventPublisher" for invocada
    E um "EventPublisher" for fornecido como argumento
    Então o "EventPublisher" será adicionado à "Lista de Publicação de Eventos"

Cenário: Invocação do Registro de Dependência
    Dado foi efetuada a "Fabricação de uma Aplicação"
    Quando o "Container" da aplicação foi obtido
    Então a operação "Container->addFactory" ou "Container->addSingleton" será invocada

Cenário: Registro da instância de uma Dependência
    Dado foi efetuada a "Invocação do Registro de Dependência"
    E um texto "identificador" for fornecido como argumento
    E a instância de uma "Dependencia" for fornecida como argumento
    Quando a operação "make" for invocada
    E o texto "identificador" for fornecido como argumento
    Então a instância da "Dependência" será retornada para uso

Cenário: Registro de uma rotina para fabricar uma Dependência
    Dado foi efetuada a "Invocação do Registro de Dependência"
    E um texto "identificador" for fornecido como argumento
    E uma "rotina" contendo a instanciação for fornecida como argumento
    Quando a operação "make" for invocada
    E o texto "identificador" for fornecido como argumento
    Então a "rotina" será invocada
    E a instância da "Dependência" gerada pela "rotina" será retornada para uso

Cenário: Registro de um valor simples como Dependência
    Dado foi efetuada a "Invocação do Registro de Dependência"
    E um texto "identificador" for fornecido como argumento
    E um "valor simples" for fornecido como argumento
    Quando a operação "make" for invocada
    E o texto "identificador" for fornecido como argumento
    Então o "valor simples" será retornada para uso

Cenário: Requisição Http para URL existente
    Dado foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" tenha sido efetuado
    E o "Registro de um Motor de I/O" tenha sido efetuado
    E o "Motor de I/O" for capaz de tratar uma "Requisição Http"
    Quando uma "Requisição Http" for efetuada
    E a aplicação for executada
    E a "URL" solicitada corresponder a uma "Ação"
    Então a "Resposta" devolvida pela "Ação" será liberada para o solicitante

Cenário: Requisição Http para URL não encontrada
    Dado foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" tenha sido efetuado
    E o "Registro do módulo secundário" tenha sido efetuado várias vezes
    E o "Registro de um Motor de I/O" tenha sido efetuado
    E o "Motor de I/O" for capaz de tratar uma "Requisição Http"
    Quando uma "Requisição Http" for efetuada
    E a aplicação for executada
    E a "URI" solicitada não possuir uma "Ação" correspondente
    Então a "Resposta" com "Http Status 404" será fabricada pelo "Módulo principal"
    E a "Resposta" será liberada para o solicitante

Cenário: Requisição Http para URL correspondente a uma Ação com erro
    Dado foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" tenha sido efetuado
    E o "Registro do módulo secundário" tenha sido efetuado várias vezes
    E o "Registro de um Motor de I/O" tenha sido efetuado
    E o "Motor de I/O" for capaz de tratar uma "Requisição Http"
    Quando uma "Requisição Http" for efetuada
    E a aplicação for executada
    E a "URI" solicitada não possuir uma "Ação" correspondente
    E a "Ação" possuir um erro fatal
    Então a "Resposta" com "Http Status 500" será fabricada pelo "Módulo principal"
    E a "Resposta" será liberada para o solicitante

    Cenário: Execução de um script de terminal
    Dado foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" tenha sido efetuado
    E o "Registro de um Motor de I/O" tenha sido efetuado
    E o "Motor de I/O" for capaz de tratar um "Comando de Terminal"
    Quando um "Comando de Terminal" for digitado
    E a aplicação for executada
    E o "Comando de Terminal" digitado corresponder a um "Script"
    Então a "Mensagem" devolvida pelo "Script" será liberada para o solicitante
    E o "Sinal de Terminal" será 0

Cenário: Execução de um script de terminal inexistente
    Dado foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" tenha sido efetuado
    E o "Registro do módulo secundário" tenha sido efetuado várias vezes
    E o "Registro de um Motor de I/O" tenha sido efetuado
    E o "Motor de I/O" for capaz de tratar um "Comando de Terminal"
    Quando um "Comando de Terminal" for digitado
    E a aplicação for executada
    E o "Comando de Terminal" digitado não possuir um "Script" correspondente
    Então a "Mensagem" com a "Ajuda do Terminal" será fabricada pelo "Motor de I/O"
    E a "Mensagem" será liberada para o solicitante
    E o "Sinal de Terminal" será 127

Cenário: Execução de um script de terminal com erro
    Dado foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" tenha sido efetuado
    E o "Registro do módulo secundário" tenha sido efetuado várias vezes
    E o "Registro de um Motor de I/O" tenha sido efetuado
    E o "Motor de I/O" for capaz de tratar um "Comando de Terminal"
    Quando um "Comando de Terminal" for digitado
    E a aplicação for executada
    E o "Comando de Terminal" digitado corresponder a um "Script"
    E o "Script" possuir um erro fatal
    Então a "Mensagem" de erro será fabricada pelo "Motor de I/O"
    E a "Mensagem" será liberada para o solicitante
    E o "Sinal de Terminal" será 126
