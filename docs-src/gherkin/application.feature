#language: pt
Funcionalidade: Instanciação
Sendo um projeto qualquer
Quero instanciar o objeto Aplicacao
Para que uma solicitação seja processada e uma resposta seja produzida

Cenário: Fabricação de uma Aplicação
    Dado que a biblioteca foi instalada no projeto
    Quando a instanciação for efetuada
    Então um "Container" será instanciado
    E uma "Lista de Módulos" será iniciada
    E uma "Lista de Publicadores de Eventos" será iniciada
    E uma "Lista de Motores" será iniciada
    E a "Zona de Tempo" será determinada para "America/Sao_Paulo"
    E o "Ambiente" será definido como "Development"

Cenário: Registro do módulo principal
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootApplication" for invocada
    E um "Bootstrap" for fornecida como argumento
    Então o "Bootstrap" será adicionado à "Lista de Módulos"
    E o "Bootstrap" será definido como "Módulo Principal"

Cenário: Registro de um módulo secundário
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootModule" for invocada
    E um "Bootstrap" for fornecida como argumento
    Então o "Bootstrap" será adicionado à "Lista de Módulos"

Cenário: Registro de um Motor de I/O
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootEngine" for invocada
    E um "Motor de I/O" for fornecido como argumento
    Então a "Lista de Módulos" será compartilhada com o "Motor de I/O"
    E o "Motor de I/O" será adicionado à "Lista de Motores"

Cenário: Registro de um Publicador de Eventos
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    Quando a operação "bootEventPublisher" for invocada
    E um "EventPublisher" for fornecido como argumento
    Então o "EventPublisher" será adicionado à "Lista de Publicação de Eventos"

Cenário: Invocação do Registro de Dependência
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    Quando o "Container" da aplicação foi obtido
    Então a operação "Container->addFactory" ou "Container->addSingleton" será invocada

Cenário: Registro da instância de uma Dependência
    Dado que foi efetuada a "Invocação do Registro de Dependência"
    E um texto "identificador" foi fornecido como argumento
    E a instância de uma "Dependencia" foi fornecida como argumento
    Quando a operação "make" for invocada
    E o texto "identificador" for fornecido como argumento
    Então a instância da "Dependência" será retornada para uso

Cenário: Registro de uma rotina para fabricar uma Dependência
    Dado que foi efetuada a "Invocação do Registro de Dependência"
    E um texto "identificador" foi fornecido como argumento
    E uma "rotina" contendo a instanciação foi fornecida como argumento
    Quando a operação "make" for invocada
    E o texto "identificador" for fornecido como argumento
    Então a "rotina" será invocada
    E a instância da "Dependência" gerada pela "rotina" será retornada para uso

Cenário: Registro de um valor simples como Dependência
    Dado que foi efetuada a "Invocação do Registro de Dependência"
    E um texto "identificador" foi fornecido como argumento
    E um "valor simples" foi fornecido como argumento
    Quando a operação "make" for invocada
    E o texto "identificador" for fornecido como argumento
    Então o "valor simples" será retornada para uso

Cenário: Execução sem Motores de I/O
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E nenhum "Motor de I/O" foi registrado
    Quando a aplicação for executada
    Então uma exceção será disparada informando a necessidade de registrar pelo menos um "Motor de I/O"
    
Cenário: Execução sem Módulo Principal
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E um "Motor de I/O" foi registrado
    E o "Módulo Principal" não foi registrado
    Quando a aplicação for executada
    Então o "Módulo Principal" tentará ser inicializado
    E uma exceção será disparada informando que um "Módulo Principal" deve ser registrado

Cenário: Execução com inicialização do Módulo Principal defeituoso
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E um "Motor de I/O" foi registrado
    E um "Módulo Principal" foi registrado
    Quando a aplicação for executada
    Então o "Módulo Principal" será inicializado
    E a inicialização do "Módulo Principal" disparará uma exceção
    Então uma exceção dirá que o "Módulo Principal" registrado causou o erro

Cenário: Execução com inicialização do Módulo Secundário defeituoso
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E um "Motor de I/O" foi registrado
    E um "Módulo Principal" foi registrado
    E um "Módulo Secundário" foi registrado
    Quando a aplicação for executada
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E a inicialização de algum "Módulo Secundário" disparar uma exceção
    Então uma exceção dirá que o "Módulo Secundário" registrado causou o erro

Cenário: Execução com inicialização do Motor I/O defeituoso
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E um "Motor de I/O" foi registrado
    E um "Módulo Principal" foi registrado usando a operação "bootApplication"
    E um "Módulo Secundário" foi registrado usando a operação "bootModule"
    Quando a aplicação for executada
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E a inicialização de algum "Motor I/O" disparam uma exceção
    Então uma exceção dirá que o "Motor I/O" registrado causou o erro

Cenário: Requisição Http para URL existente
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" foi efetuado
    E o "Registro de um Motor de I/O" foi efetuado
    Quando a aplicação for executada
    E uma "Requisição Http" for fornecida como argumento
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E algum "Motor de I/O" for capaz de tratar uma "Requisição Http"
    E a "URL" solicitada corresponder a uma "Ação"
    Então a "Resposta" devolvida pela "Ação" será liberada para o solicitante

Cenário: Requisição Http para URL não encontrada
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" foi efetuado
    E o "Registro do módulo secundário" foi efetuado várias vezes
    E o "Registro de um Motor de I/O" foi efetuado
    Quando a aplicação for executada
    E uma "Requisição Http" for fornecida como argumento
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E algum "Motor de I/O" for capaz de tratar uma "Requisição Http"
    E a "URI" solicitada não possuir uma "Ação" correspondente
    Então a "Resposta" com "Http Status 404" será fabricada pelo "Módulo principal"
    E a "Resposta" será liberada para o solicitante

Cenário: Requisição Http para URL correspondente a uma Ação com erro
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" foi efetuado
    E o "Registro do módulo secundário" foi efetuado várias vezes
    E o "Registro de um Motor de I/O" foi efetuado
    Quando a aplicação for executada
    E uma "Requisição Http" for fornecida como argumento
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E algum "Motor de I/O" for capaz de tratar uma "Requisição Http"
    E a "URI" solicitada não possuir uma "Ação" correspondente
    E a "Ação" possuir um erro fatal
    Então a "Resposta" com "Http Status 500" será fabricada pelo "Módulo principal"
    E a "Resposta" será liberada para o solicitante

Cenário: Execução de um script de terminal
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" foi efetuado
    E o "Registro de um Motor de I/O" foi efetuado
    Quando a aplicação for executada
    E uma "Entrada de Terminal" contendo os argumentos for fornecida
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E o "Motor de I/O" for capaz de tratar um "Comando de Terminal"E algum "Motor de I/O" for capaz de tratar uma "Requisição Http"
    E o "Comando de Terminal" digitado corresponder a um "Script"
    Então a "Mensagem" devolvida pelo "Script" será liberada para o solicitante
    E o "Sinal de Terminal" será 0

Cenário: Execução de um script de terminal inexistente
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" foi efetuado
    E o "Registro do módulo secundário" foi efetuado várias vezes
    E o "Registro de um Motor de I/O" foi efetuado
    Quando a aplicação for executada
    E uma "Entrada de Terminal" contendo os argumentos for fornecida
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E o "Motor de I/O" for capaz de tratar um "Comando de Terminal"E algum "Motor de I/O" for capaz de tratar uma "Requisição Http"
    E o "Comando de Terminal" digitado não possuir um "Script" correspondente
    Então a "Mensagem" com a "Ajuda do Terminal" será fabricada pelo "Motor de I/O"
    E a "Mensagem" será liberada para o solicitante
    E o "Sinal de Terminal" será 127

Cenário: Execução de um script de terminal com erro
    Dado que foi efetuada a "Fabricação de uma Aplicação"
    E o "Registro do módulo principal" foi efetuado
    E o "Registro do módulo secundário" foi efetuado várias vezes
    E o "Registro de um Motor de I/O" foi efetuado
    Quando a aplicação for executada
    E uma "Entrada de Terminal" contendo os argumentos for fornecida
    E o "Módulo Principal" for inicializado
    E os "Módulos Secundários" forem inicializados
    E os "Motores I/O" forem inicializados com os "Módulos" correspondentes
    E o "Motor de I/O" for capaz de tratar um "Comando de Terminal"E algum "Motor de I/O" for capaz de tratar uma "Requisição Http"
    E o "Comando de Terminal" digitado corresponder a um "Script"
    E o "Script" possuir um erro fatal
    Então a "Mensagem" de erro será fabricada pelo "Motor de I/O"
    E a "Mensagem" será liberada para o solicitante
    E o "Sinal de Terminal" será 126
