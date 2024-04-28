# language: pt

Funcionalidade: Resposta Server Error da aplicação
    Eu como aplicação
    Quero processar uma solicitação
    Para que uma resposta adequada seja devolvida para o cliente

Cenário: Resposta Server Error Html
    Dado uma aplicação Diactoros
    E com arquitetura Mvc
    E o tipo solicitado for Html
    Quando a solicitação for executada
    Então a resposta será 500
    E a resposta conterá "ResponseErrorHtml.txt"
    E a resposta será do tipo Html

Cenário: Resposta Server Error Json
    Dado uma aplicação Diactoros
    E com arquitetura Mvc
    E o tipo solicitado for Json
    Quando a solicitação for executada
    Então a resposta será 500
    E a resposta conterá "ResponseErrorJson.txt"
    E a resposta será do tipo Json

Cenário: Resposta Server Error Text
    Dado uma aplicação Diactoros
    E com arquitetura Mvc
    E o tipo solicitado for Text
    Quando a solicitação for executada
    Então a resposta será 500
    E a resposta conterá "ResponseErrorText.txt"
    E a resposta será do tipo Text

Cenário: Resposta Server Error Xml
    Dado uma aplicação Diactoros
    E com arquitetura Mvc
    E o tipo solicitado for Xml
    Quando a solicitação for executada
    Então a resposta será 500
    E a resposta conterá "ResponseErrorXml.txt"
    E a resposta será do tipo Xml

