# language: pt

Funcionalidade: Respostas Server Error
    Eu como usuário
    Quero fazer uma solicitação para uma aplicação com erro
    Para que a resposta adequada seja devolvida no formato solicitado

# Esquema do Cenário: Server Error Default
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E bootstrap com dependência Session e <httpFactory>
#     Quando a aplicação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "<message>"
#     E a resposta será do tipo "text/html"
#     Exemplos:
#         | engine          | httpFactory          | message        |
#         | FrontController | DiactorosHttpFactory | Fc500Html.txt  |
#         | FrontController | GuzzleHttpFactory    | Fc500Html.txt  |
#         | FrontController | NyHolmHttpFactory    | Fc500Html.txt  |
#         | Mvc             | DiactorosHttpFactory | Mvc500Html.txt |
#         | Mvc             | GuzzleHttpFactory    | Mvc500Html.txt |
#         | Mvc             | NyHolmHttpFactory    | Mvc500Html.txt |

# Esquema do Cenário: Server Error HTML
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E o tipo solicitado em <httpFactory> for "text/html"
#     E bootstrap com dependência Session e <httpFactory>
#     Quando a aplicação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "<message>"
#     E a resposta será do tipo "text/html"
#     Exemplos:
#         | engine          | httpFactory          | message        |
#         | FrontController | DiactorosHttpFactory | Fc500Html.txt  |
#         | FrontController | GuzzleHttpFactory    | Fc500Html.txt  |
#         | FrontController | NyHolmHttpFactory    | Fc500Html.txt  |
#         | Mvc             | DiactorosHttpFactory | Mvc500Html.txt |
#         | Mvc             | GuzzleHttpFactory    | Mvc500Html.txt |
#         | Mvc             | NyHolmHttpFactory    | Mvc500Html.txt |

# Esquema do Cenário: Server Error JSON
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E bootstrap com dependência Session e <httpFactory>
#     E o tipo solicitado em <httpFactory> for "application/json"
#     Quando a aplicação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "<message>"
#     E a resposta será do tipo "application/json"
#     Exemplos:
#         | engine          | httpFactory          | message        |
#         | Mvc             | DiactorosHttpFactory | Mvc500Json.txt |
#         | Mvc             | GuzzleHttpFactory    | Mvc500Json.txt |
#         | Mvc             | NyHolmHttpFactory    | Mvc500Json.txt |
#         | FrontController | DiactorosHttpFactory | Fc500Json.txt  |
#         | FrontController | GuzzleHttpFactory    | Fc500Json.txt  |
#         | FrontController | NyHolmHttpFactory    | Fc500Json.txt  |

# Esquema do Cenário: Server Error TEXT
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E bootstrap com dependência Session e <httpFactory>
#     E o tipo solicitado em <httpFactory> for "text/plain"
#     Quando a aplicação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "<message>"
#     E a resposta será do tipo "text/plain"
#     Exemplos:
#         | engine          | httpFactory          | message        |
#         | Mvc             | DiactorosHttpFactory | Mvc500Text.txt |
#         | Mvc             | GuzzleHttpFactory    | Mvc500Text.txt |
#         | Mvc             | NyHolmHttpFactory    | Mvc500Text.txt |
#         | FrontController | DiactorosHttpFactory | Fc500Text.txt  |
#         | FrontController | GuzzleHttpFactory    | Fc500Text.txt  |
#         | FrontController | NyHolmHttpFactory    | Fc500Text.txt  |

# Esquema do Cenário: Server Error XML
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E bootstrap com dependência Session e <httpFactory>
#     E o tipo solicitado em <httpFactory> for "application/xml"
#     Quando a aplicação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "<message>"
#     E a resposta será do tipo "application/xml"
#     Exemplos:
#         | engine          | httpFactory          | message        |
#         | Mvc             | DiactorosHttpFactory | Mvc500Xml.txt |
#         | Mvc             | GuzzleHttpFactory    | Mvc500Xml.txt |
#         | Mvc             | NyHolmHttpFactory    | Mvc500Xml.txt |
#         | FrontController | DiactorosHttpFactory | Fc500Xml.txt  |
#         | FrontController | GuzzleHttpFactory    | Fc500Xml.txt  |
#         | FrontController | NyHolmHttpFactory    | Fc500Xml.txt  |

Esquema do Cenário: Server Error Engine
    Dado uma aplicação instanciada
    E com mecanismo <engine>
    E bootstrap com dependência Session e <httpFactory>
    E com rota "/test/error"
    Quando a aplicação for executada
    Então a resposta terá status 500
    E a resposta conterá "<message>"
    E a resposta será do tipo "text/html"
    Exemplos:
        | engine          | httpFactory          | message        |
        | FrontController | DiactorosHttpFactory | Error: Exceção lançada na execução do recurso solicitado |
        | FrontController | GuzzleHttpFactory    | Error: Exceção lançada na execução do recurso solicitado |
        | FrontController | NyHolmHttpFactory    | Error: Exceção lançada na execução do recurso solicitado |
        | Mvc             | DiactorosHttpFactory | Error: Exceção lançada na execução do recurso solicitado |
        | Mvc             | GuzzleHttpFactory    | Error: Exceção lançada na execução do recurso solicitado |
        | Mvc             | NyHolmHttpFactory    | Error: Exceção lançada na execução do recurso solicitado |

    # Exemplos:
    #     | engine          | httpFactory          | message        |
    #     | FrontController | DiactorosHttpFactory | Fc500Html.txt  |
    #     | FrontController | GuzzleHttpFactory    | Fc500Html.txt  |
    #     | FrontController | NyHolmHttpFactory    | Fc500Html.txt  |
    #     | Mvc             | DiactorosHttpFactory | Mvc500Html.txt |
    #     | Mvc             | GuzzleHttpFactory    | Mvc500Html.txt |
    #     | Mvc             | NyHolmHttpFactory    | Mvc500Html.txt |