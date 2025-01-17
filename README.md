# Sistema de gerencimaneto de assinaturas

## Setup do projeto 🚀

### Inicialização

Para começar, instale as dependências do projeto executando o seguinte comando:

```
composer install
```

Com as dependências instaladas, execute o seguinte comando para iniciar o projeto:

```
sudo make start
```

Após os containers serem iniciados, a API pode ser acessada em http://localhost/api.

Pronto! Agora basta testar as requisições na coleção que foi enviada junto ao teste!

A coleção está nomeada como "CollectionInsomnia.json" (O Token gerado na autenticação deveria ir
automaticamente para a header, mas parece que ao exportar a collection, esse script deixa de funcionar,
neste caso, só substituir o script no índice "Authorization" da header das rotas protegidas)

Para visualizar o texto de explicação de decisões e expansões do projeto "ProjectExpanation.txt"

## Construído com

* [PHP](https://www.php.net/)
* [Laravel](https://laravel.com/)
* [Laravel Sail](https://laravel.com/docs/11.x/sail)
* [MySQL](https://www.mysql.com/)
* [Docker](https://www.docker.com/)
