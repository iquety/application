# language: pt

Funcionalidade: Resposta Not Found do mecanismo Mvc
    Eu usuário
    Quero solicitar um recurso inexistente a uma aplicação Mvc
    Para que a resposta adequada seja devolvida no formato solicitado

# Cenário: Resposta Mvc Not Found Default
#     Dado um mecanismo Mvc
#     E uma rota configurada
#     Quando for solicitada uma rota inexistente
#     Então a resposta será Not Found
#     E a resposta Not found conterá "Response404Html.txt"
#     E a resposta Not found será do tipo Html

# Cenário: Resposta Mvc Not Found Html
#     Dado um mecanismo Mvc
#     E uma rota configurada
#     E o tipo solicitado for Html
#     Quando for solicitada uma rota inexistente
#     Então a resposta será Not Found
#     E a resposta Not found conterá "Response404Html.txt"
#     E a resposta Not found será do tipo Html

# Cenário: Resposta Server Error Html
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Html
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "ResponseErrorHtml.txt"
#     E a resposta será do tipo Html

# Cenário: Resposta Server Error Json
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Json
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "ResponseErrorJson.txt"
#     E a resposta será do tipo Json

# Cenário: Resposta Server Error Text
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Text
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "ResponseErrorText.txt"
#     E a resposta será do tipo Text

# Cenário: Resposta Server Error Xml
#     Dado uma aplicação Diactoros
#     E com arquitetura Mvc
#     E o tipo solicitado for Xml
#     Quando a solicitação for executada
#     Então a resposta será 500
#     E a resposta conterá "ResponseErrorXml.txt"
#     E a resposta será do tipo Xml

