# language: pt

Funcionalidade: Resposta Server Error da aplicação
    Eu como usuário
    Quero fazer uma solicitação para uma aplicação com erro
    Para que a resposta adequada seja devolvida no formato solicitado

# Esquema do Cenário: 
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     Quando a solicitação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "Response500Html.txt"
#     E a resposta será do tipo "text/html"

# Esquema do Cenário: Resposta Server Error Default
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E sem Bootstrap
#     Quando a aplicação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "Response500Html.txt"
#     E a resposta será do tipo "text/html"
#     Exemplos:
#         | engine |
#         | FrontController |
#         | Mvc |


# Esquema do Cenário: Resposta Server Error Default
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E Bootstrap completo com <httpFactory>
#     Quando a aplicação for executada
#     Então será emitida uma exceção do tipo "RuntimeException" 
#     E a exceção conterá a mensagem "No bootstrap specified for the application"
#     E o Container possuirá dependência Session
#     E o Container possuirá dependência HttpFactory

# Esquema do Cenário: Resposta Server Error Default
#     Dado uma aplicação instanciada
#     E com mecanismo <engine>
#     E Bootstrap completo com <httpFactory>
#     Quando a aplicação for executada
#     Então será emitida uma exceção do tipo "RuntimeException" 
#     E a exceção conterá a mensagem "No bootstrap specified for the application"
#     E o Container possuirá dependência Session
#     E o Container possuirá dependência HttpFactory
    
#     Exemplos:
#         | engine          | httpFactory          |
#         | FrontController | DiactorosHttpFactory |
#         | FrontController | GuzzleHttpFactory    |
#         | FrontController | NyHolmHttpFactory    |
#         | Mvc | DiactorosHttpFactory             |
#         | Mvc | GuzzleHttpFactory                |
#         | Mvc | NyHolmHttpFactory                |


# Cenário: 
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     Quando a solicitação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "Response500Html.txt"
#     E a resposta será do tipo "text/html"


# Cenário: Resposta Server Error Html
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Html
#     Quando a solicitação for executada
#     Então a resposta terá status 500
#     E a resposta conterá "Response500Html.txt"
#     E a resposta será do tipo Html

# Cenário: Resposta Server Error Json
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Json
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "Response500Json.txt"
#     E a resposta será do tipo Json

# Cenário: Resposta Server Error Text
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Text
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "Response500Text.txt"
#     E a resposta será do tipo Text

# Cenário: Resposta Server Error Xml
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Xml
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "Response500Xml.txt"
#     E a resposta será do tipo Xml

