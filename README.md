# cache
Biblioteca que cacheia resultados de métodos, geralmente consultas a banco de dados. Esta biblioteca funciona como um conector para o backend memcached. O servidor roda na máquina local.

Pode ser usado em métodos de classes instanciadas e em métodos estáticos.

## Requisitos

1. memcached
2. php-memcached



## Instalação e configuração

Instalação do servidor memcached
* no ubuntu, para instalar o memcached use ```apt install memcached php-memcached```
* acrescente no ```/etc/memcached.conf``` a linha ```I = 5M ``` para aumentar para 5MB o tamanho de cada objeto ou outro valor que achar conveniente
* reinicie o serviço ``` service memcached restart ```
* reinicie o apache ``` service apache2 reload ```

Instalação da biblioteca
* Para ubuntu use ``` apt install php-memcached ```
* ``` composer require USPdev/cache ```

## Utilização

Caso típico de uma consulta ao BD:

    $pessoa = new Pessoa();
    $lista = $pessoa->lista();

Usando o cache, a consulta fica assim:

    $pessoa = new Pessoa();
    $cache = new cache($pessoa);
    $lista = $cache->getCached('lista','');

Se o método for estático fica assim:

    $cache = new cache();
    $lista = $cache->getCached('Pessoa::lista','');
