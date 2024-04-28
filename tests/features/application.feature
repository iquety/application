# language: pt

Funcionalidade: Execução da aplicação
    Eu como bootstrap do sistema
    Quero executar uma nova aplicação
    Para que os módulos usem as funcionaliades disponíveis

Cenário: Executar sem um mecanismo Web
    Dado uma aplicação instanciada
    Quando a aplicação for executada
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção possui a mensagem "No web engine to handle the request"

# FRONT CONTROLLER

Esquema do Cenário: Executar sem Bootstrap
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E sem Bootstrap
    Quando a aplicação for executada
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção possui a mensagem "No bootstrap specified for the application"
    Exemplos:
        | engine |
        | FrontController |
        | Mvc |

Esquema do Cenário: Executar com Bootstrap com exceção
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E Bootstrap com Exceção
    Quando a aplicação for executada
    E Container não terá dependência Session
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção possui a mensagem "The bootApplication method failed"
    Exemplos:
        | engine |
        | FrontController |
        | Mvc |

Esquema do Cenário: Executar com Bootstrap mas sem Session
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E Bootstrap sem dependências
    Quando a aplicação for executada
    E Container não terá dependência Session
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    Exemplos:
        | engine          | message |
        | FrontController | "Please provide an implementation for the dependency Session" |
        | Mvc             | "Please provide an implementation for the dependency Session" |

Esquema do Cenário: Executar com Bootstrap com Session inválida
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E Bootstrap com dependência Session inválida
    Quando a aplicação for executada
    E Container terá dependência Session
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    Exemplos:
        | engine | message |
        | FrontController | "Session dependency in the bootstrap provided in the Application->bootApplication method is invalid" |
        | Mvc             | "Session dependency in the bootstrap provided in the Application->bootApplication method is invalid" |

Esquema do Cenário: Executar com Bootstrap com Session mas sem HttpFactory
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E Bootstrap com dependência Session
    Quando a aplicação for executada
    E Container terá dependência Session
    E Container não terá dependência HttpFactory
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    Exemplos:
        | engine          | message |
        | FrontController | "Please provide an implementation for the dependency HttpFactory" |
        | Mvc             | "Please provide an implementation for the dependency HttpFactory" |

Esquema do Cenário: Executar com Bootstrap com Session válida e HttpFactory inválida
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E Bootstrap com dependência Session e HttpFactory inválida
    Quando a aplicação for executada
    E Container terá dependência Session
    E Container terá dependência HttpFactory
    Então imite uma exceção do tipo "RuntimeException" 
    E a exceção conterá a mensagem <message>
    Exemplos:
        | engine          | message | 
        | FrontController | "HttpFactory dependency in the bootstrap provided in the Application->bootApplication method is invalid" |
        | Mvc             | "HttpFactory dependency in the bootstrap provided in the Application->bootApplication method is invalid" |

Esquema do Cenário: Executar sem rotas ou comandos
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E Bootstrap com dependência Session e HttpFactory
    Quando a aplicação for executada
    E Container terá dependência Session
    E Container terá dependência HttpFactory
    E Container terá dependência Application
    E Container terá dependência ServerRequestInterface
    Exemplos:
        | engine          |
        | FrontController |
        | Mvc             |
