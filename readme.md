# Freep Application

## Sobre

O Freep Aplication é uma biblioteca para a criação de aplicações modulares, com
as seguintes características:

### Aplicação:

* Proporciona a separação de interesses, usando módulos bootáveis;
* Baseada no padrão arquitetural MVC;
* Dependências extremamente flexíveis, usando arquitetura Hexagonal (Ports and Adapters).

### Módulo

- Pode definir suas próprias rotas;
- Pode definir suas próprias dependências;
- Suas dependências são fabricadas apenas se uma rota do módulo for acessada;
- Carrega Controladores e Policies usando o padrão de Inversão de Controle.
