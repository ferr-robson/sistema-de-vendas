# Sistema de Vendas - Backend

## Índice

- [Sobre o sistema](#sobre-o-sistema)
- [Como iniciar o sistema localmente em ambiente de desenvolvimento](#como-iniciar-o-sistema-localmente-em-ambiente-de-desenvolvimento)
- [Rotas utilizadas e cofigurações do request](#rotas-utilizadas-e-cofigurações-do-request)
- [Teste](#testes)

## Sobre o Sistema

O sistema de vendas apresentado nesse projeto busca gerenciar o processo de vendas de uma loja. Nele, é possível fazer:

- Cadastro e atualização dos vendedores
- Cadastro e atualização dos clientes
- Cadastro e atualização das formas de pagamento, além das já existentes no sistema (Pix, Boleto, Cartão de Crédito, Cartão de Débito)
- Cadastro e atualização das vendas 
- Cadastro e atualização dos produtos
- Adição, edição e remoção de parcelas
- Adição, edição e remoção de itens do carrinho de compras
- Gerar um PDF com os dados referentes à uma venda em específico

Esta documentação refere-se ao backend da aplicação e foi desenvolvida com o framework [Laravel](https://laravel.com/).
 
## Como iniciar o sistema localmente em ambiente de desenvolvimento

1. Clonar a última versão do projeto
2. Instalar [MySQL](https://www.mysql.com/products/workbench/), [PHP](https://www.php.net/) e [Composer](https://getcomposer.org/)
3. Acessar, via terminal, o diretório com o projeto clonado e executar o comando:

```sh
composer global require laravel/installer
```

4.  Baixar as dependências do projeto, com o comando

```sh
composer install
```

5. Crie um banco de dados, no MySQL
6. Duplique o arquivo .env.example, encontrado na raiz do projeto e renomeie a cópia para .env
7. No arquivo .env, configure as variáveis DB_DATABASE e DB_PASSWORD, informando o nome do banco de dados que foi criado e a senha do MySQL, como mostra o exemplo abaixo:

```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=congressosufla_api
DB_USERNAME=root
DB_PASSWORD=
```

8. Execute as migrações e as seeders com o comando:

```sh
php artisan migrate --seed
```

9. Inicie a api localmente

```sh
php artisan serve
```

## Rotas utilizadas e cofigurações do request

As rotas utilizadas, em sua maioria, foram criadas com o padrão Route::resource. As exceções que fogem desse padrão são as rotas de login, e a rota de PDF-venda
Rotas que esperam autenticação /cliente, /produto, /venda, /forma-pagamento, /item-venda, /parcela. Elas esperam, no cabeçalho da requisição, a chave 'Authorization', com o valor 'Bearer {token retornado pela rota de login}'. Exemplo: 'Bearer 1|OVR1ywGyyyIuTpRejMyvVBL76TyEOVwMa6g26I7L16f55ff6'

### Parâmetros esperados por cada rota

/usuario

post: 'name', 'email', 'password', 'password_confirmation'

put/patch: 'name', 'email', 'password'


/cliente

post: 'nome', 'email'

put/patch: 'nome', 'email'


/produto

post: 'nome', 'preco'

put/patch: 'nome', 'preco'


/venda

post: 'cliente' (id do cliente), 'forma_pagamento' (id de uma das formas de pagamento), 'total_venda' (valor total da venda), 'parcelado' (valor booleano), 'produtos' (array dos produtos comprados, onde cada item do array é um array que possui os campos 'produto_id' e 'quantidade'), 'qtde_parcelas'

put/patch: 'cliente' (id do cliente), 'forma_pagamento' (id de uma das formas de pagamento), 'total_venda' (valor total da venda), 'parcelado' (valor booleano), 'produtos' (array dos produtos comprados, onde cada item do array é um array que possui os campos 'produto_id' e 'quantidade'), 'qtde_parcelas'


/forma-pagamento

post: 'nome'

put/patch: 'nome'


/item-venda

post: 'produto_id', 'venda_id', 'quantidade' (quantidade de itens adicionados)

put/patch: 'produto_id', 'quantidade' (quantidade de itens)


/parcela

post: 'venda_id', 'data_vencimento', 'valor_parcela'

put/patch: 'venda_id', 'data_vencimento', 'valor_parcela'

### Rota de login

localhost:8000/api/login
Espera os parâmetros 'email' e 'password', referentes à um usuário (vendedor) que exista no banco de dados

### Rota PDF-venda

localhost:8000/pdf-venda/{id}
Ira gerar um PDF view. Basta passar o id de uma venda existente, no link.

## Testes 

```sh
php artisan test
```
