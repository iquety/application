# language: pt

Funcionalidade: Execução da aplicação
    Eu como bootstrap do sistema
    Quero executar uma nova aplicação
    Para que as dependências sejam validadas e disponibilizadas

Cenário: Executar sem um mecanismo web
    Dado uma aplicação instanciada
    E sem mecanismo web
    E sem bootstrap
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem "No web engine to handle the request"

Esquema do Cenário: Executar sem Bootstrap
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E sem bootstrap
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem "No bootstrap specified for the application"
    E o container não possuirá dependência Session
    E o container não possuirá dependência HttpFactory
    Exemplos:
        | engine |
        | FrontController |
        | Mvc |

Esquema do Cenário: Executar com Bootstrap com exceção
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap com exceção
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem "The bootApplication method failed"
    E o container não possuirá dependência Session
    E o container não possuirá dependência HttpFactory
    Exemplos:
        | engine |
        | FrontController |
        | Mvc |

Esquema do Cenário: Executar com Bootstrap mas sem Session
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap sem dependências
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    E o container não possuirá dependência Session
    E o container não possuirá dependência HttpFactory
    Exemplos:
        | engine          | message |
        | FrontController | "Please provide an implementation for the dependency Session" |
        | Mvc             | "Please provide an implementation for the dependency Session" |

Esquema do Cenário: Executar com Bootstrap com Session inválida
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap com dependência Session inválida
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    E o container possuirá dependência Session
    Exemplos:
        | engine | message |
        | FrontController | "Session dependency in the bootstrap provided in the Application->bootApplication method is invalid" |
        | Mvc             | "Session dependency in the bootstrap provided in the Application->bootApplication method is invalid" |

Esquema do Cenário: Executar com Bootstrap com Session mas sem HttpFactory
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap com dependência Session
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    E o container possuirá dependência Session
    E o container não possuirá dependência HttpFactory
    Exemplos:
        | engine          | message |
        | FrontController | "Please provide an implementation for the dependency HttpFactory" |
        | Mvc             | "Please provide an implementation for the dependency HttpFactory" |

Esquema do Cenário: Executar com Bootstrap com Session válida e HttpFactory inválida
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap com dependência Session e HttpFactory inválida
    Quando a aplicação for executada
    Então será emitida uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    E o container possuirá dependência Session
    E o container possuirá dependência HttpFactory
    Exemplos:
        | engine          | message | 
        | FrontController | "HttpFactory dependency in the bootstrap provided in the Application->bootApplication method is invalid" |
        | Mvc             | "HttpFactory dependency in the bootstrap provided in the Application->bootApplication method is invalid" |

Esquema do Cenário: Executar sem rotas ou comandos
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap com dependência Session e <httpFactory>
    Quando a aplicação for executada
    Então o container possuirá dependência Session
    E o container possuirá dependência HttpFactory
    E o container possuirá dependência Application
    E o container possuirá dependência ServerRequestInterface
    Exemplos:
        | engine          | httpFactory          |
        | FrontController | DiactorosHttpFactory |
        | FrontController | GuzzleHttpFactory    |
        | FrontController | NyHolmHttpFactory    |
        | Mvc | DiactorosHttpFactory             |
        | Mvc | GuzzleHttpFactory                |
        | Mvc | NyHolmHttpFactory                |
