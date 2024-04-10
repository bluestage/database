# Manual de Utilização da Biblioteca Database em PHP

Este manual descreve como utilizar a classe `Database` para realizar operações de consulta, inserção, atualização, exclusão e depuração (debug) em um banco de dados MySQL utilizando PHP.

## Instalação

Para utilizar a classe `Database`, siga estas etapas:

1. Baixe o arquivo `Database.php`.
2. Inclua o arquivo em seu projeto PHP usando a instrução `require_once`.
3. Configure as credenciais de acesso ao banco de dados no construtor da classe.

Exemplo de inclusão do arquivo e configuração das credenciais:

```php
<?php
require_once 'Database.php';

$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'senha',
    'database' => 'nome_do_banco',
];

$db = new Database($config);
?>
