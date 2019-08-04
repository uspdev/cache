# cache
Biblioteca que cacheia resultados de métodos, geralmente consultas a banco de dados. Esta biblioteca funciona como um conector para o backend memcached mas pode ser aplicado a outros backends.



## Requisitos

1. memcached
    * no ubuntu, para instalar o memcached use ```apt install memcached php-memcached```
    * acrescente no ```/etc/memcached.conf``` a linha ```I = 5M ``` para aumentar para 5MB o tamanho de cada objeto ou outro valor que achar conveniente
    * reinicie o serviço ``` service memcached restart ```
    * reinicie o apache ``` service apache2 reload ```

## Instalação e configuração

Instale pelo composer

    composer require USPdev/cache;


## Utilização

Caso típico

    $pessoa = new Pessoa();
    $pessoas = $pessoa->lista();

Usando o cache

    $pessoa = new cache(new Pessoa());
    $pessoas = $pessoa->getCached('lista','');
