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
```

## Operações Básicas

A classe `Database` oferece métodos para realizar as seguintes operações básicas:

### Select

```php
<?php
// Selecionar todos os registros da tabela "users"
$result = $db->select('users');
print_r($result);
?>```

### Insert

```php
<?php
// Inserir um novo registro na tabela "products"
$data = [
    'name' => 'Produto A',
    'price' => 19.99,
];
$db->insert('products', $data);
?>
```

### Update

```php
<?php
// Atualizar o preço do produto com ID 1 para 29.99 na tabela "products"
$data = [
    'price' => 29.99,
];
$db->update('products', $data, ['id' => 1]);
?>
```

### Delete

```php
<?php
// Excluir o registro com ID 2 da tabela "orders"
$db->delete('orders', ['id' => 2]);
?>
```

### Has

```php
<?php
// Verificar se há registros na tabela "customers" com nome "John"
if ($db->has('customers', ['name' => 'John'])) {
    echo "John está na lista de clientes.";
} else {
    echo "John não está na lista de clientes.";
}
?>
```

## Operações Avançadas

A classe `Database` também oferece métodos para operações mais avançadas:

### Limit e Sort

```php
<?php
// Selecionar os 10 primeiros registros da tabela "products" ordenados por preço decrescente
$result = $db->select('products', '*', [], 10)->sort('price', 'DESC');
print_r($result);
?>
``` 

### Where com Condições Personalizadas

```php
<?php
// Selecionar registros da tabela "orders" com valor total superior a 100
$result = $db->select('orders', '*', ['total[>]' => 100]);
print_r($result);
?>
``` 

## Debug

A depuração (debug) pode ser ativada para visualizar as consultas SQL geradas:

```php
<?php
// Ativar o modo de debug
$db->debug();

// Realizar uma consulta
$result = $db->select('products', '*', ['category' => 'Electronics']);
?>
```

## Exemplo Completo

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

// Exemplo de utilização
$result = $db->select('products', '*', ['category' => 'Electronics']);
print_r($result);
?>
````

