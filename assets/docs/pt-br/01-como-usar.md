# Como usar

--page-nav--

## O interpretador

O Docmap é um interpretador, que analisa um projeto de documentação em markdown.

No processo, são analisadas a estrutura de diretórios e o conteúdo dos arquivos em busca de informações que forneçam a forma como os menus deverão ser desenhados.

## Intalação

O Docmap é usado através da linha de comando. Para isso ser possível, é necessário instalar o pacote do composer conforme o exemplo abaixo:

```bash
composer require ricardopedias/freep-docmap
```

## Execução

Após a instalação, o script se encontrará em `vendor/bin/docmap`, de onde poderá ser utilizado em qualquer projeto PHP, invocando-o como no exemplo a seguir:

```bash
vendor/bin/docmap -s src-docs -d docs -r ../../readme.md -l pt-br
```

O comando acima significa:

Parte | Descrição
-- | --
vendor/bin/docmap | A invocação do comando instalado no diretório vendor/bin do composer
-s src-docs | Especifica o caminho do diretório contendo os arquivos markdown com tags de substituição
-d docs | Especifica o caminho do diretório onde os arquivos processados serão salvos
-r ../../readme.md | Especifica a localização do arquivo de "apresentação" do projeto
-l pt-br | Determina o idioma para a tradução dos itens de menu. Atualmente, pode ser 'en' ou 'pt-br'

## Automação

A melhor maneira de usar o Docmap em um projeto é configurando os comandos necessários em uma rotina no arquivo **composer.json** para que possa ser executada sempre que uma atualização for efetuada na documentação.

Suponha que o projeto possua a seguinte estrutura de diretórios:

![Estrutura de diretórios com documentação markdown](../imgs/directories.png)

Diretório | Objetivo
-- | --
assets/docs | Diretório contendo os documentos em seu estado original, contendo tags de substituição
assets/docs/en | Documentação original em inglês
assets/docs/pt-br | Documentação original em português
docs | Documentação gerada pelo Docmap a partir dos diretórios anteriores
readme.md | Localização do arquivo readme do projeto

Neste caso, o projeto disponibilizará a documentação em dois idiomas. Cada idioma está em um diretório separado para maior organização.

No arquivo `composer.json`, pode-se criar uma rotina para remover a documentação atual e gerá-la novamente. Observe o exemplo abaixo, na seção **scripts**, a rotina **docmap**:

```json
// composer.json

{
    "require": {
        "php": "^8.0.0",
        "ricardopedias/freep-docmap": "dev-main"
    },
    "scripts" : {
        "docmap" : [
            "rm -Rf docs/pt-br/*",
            "vendor/bin/docmap -s assets/docs/pt-br -d docs/pt-br -r leiame.md -l pt-br",
            "rm -Rf docs/en/*",
            "vendor/bin/docmap -s assets/docs/en -d docs/en"
        ]
    }
}
```

Após esta configuração, para refazer a documentação nos dois idiomas, basta invocar o composer fornecendo o nome **docmap**:

```shell
composer docmap
```

--page-nav--
