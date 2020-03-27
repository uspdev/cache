# Cache

Biblioteca que cacheia resultados de métodos, geralmente consultas a banco de dados. Esta biblioteca funciona como um conector para o backend memcached. O servidor roda na máquina local.

Pode ser usado em métodos de classes instanciadas e em métodos estáticos.

Os dados ficam guardados no cache em função dos parâmetros passados para o método. Mudando um parâmetro é feito um novo cache.

Por padrão os caches expiram depois de 4 horas ou reiniciando o servidor memcached.

Por padrão dados menores que 32 bytes não serão cacheados.

## Requisitos

* PHP > 7.0
* memcached
* php-memcached

## Instalação e configuração

### Instalação do servidor memcached

* No ubuntu 1804, debian 9, debian 10: para instalar o memcached use ```apt install memcached```
* Acrescente no ```/etc/memcached.conf``` a linha ```I = 5M``` para aumentar para 5MB o tamanho de cada objeto ou outro valor que achar conveniente
* O memcached (no ubuntu) vem configurado para utilizar cerca de 64MB de RAM (não é um limite rígido). Caso seu servidor suporte e tenha demanda, mude no arquivo de configuração ```-m 128``` para dobrar o valor padrão
* Reinicie o serviço ```service memcached restart```

OBS.: Ferramentas para linha de comando podem ser obtidas no pacote libmemcached-tools.

### Instalação da biblioteca

* Para ubuntu 1804, debian 9 ou debian 10 use ```apt install php-memcached```
* reinicie o apache ```service apache2 reload```
* Coloque a biblioteca como dependência ```composer require uspdev/cache```

## Utilização

### Objetos instanciados

Caso típico de uma consulta ao BD:

```php
$pessoa = new Pessoa();
$lista = $pessoa->lista('nome');
```

Usando o cache, a consulta fica assim:

```php
use Uspdev\Cache\Cache;

$pessoa = new Pessoa();
$cache = new Cache($pessoa);
$lista = $cache->getCached('lista',['nome']);
```

ou 

```php
$lista = $cache->getCached('lista','nome');
```

Isso quer dizer que se o método pessoa->Lista() aceitar mais de um parâmetro você pode passar a lista de parâmetros como array que o cache vai transformar em uma lista de argumentos.

### Métodos estáticos

Se o método for estático fica assim:

```php
use Uspdev\Cache\Cache;

$cache = new Cache();
$lista = $cache->getCached('Pessoa::lista',['nome']);
```

### Outros métodos

Para ver algumas informações úteis sobre o cache use

```php
$status = $cache->status();
```

Caso sua aplicação tenha alterado o conteúdo de uma consulta cacheada, você deve invalidar o cache para não receber dados obsoletos.

```php
$status = $cache->flush();
```

## Testes

Rode alguns testes para ver o funcionamento.

```bash
php test/runtests.php
```

## Parâmetros de configuração

Se você não tem o servidor memcached disponível num projeto que utiliza cache, você pode desabilitar o cache completamente de forma a poder continuar usando o projeto.

Em ambiente de desenvolvimento também pode ser desejável ter o cache desabilitado.

Para desabilitar o cache, defina a variável de ambiente ou a constante abaixo.

```php
putenv('USPDEV_CACHE_DISABLE=1');
```

ou

```php
define('USPDEV_CACHE_DISABLE', true);
```

Dessa forma a biblioteca de cache vai fazer apenas um bypass das consultar que passarem por ela.

O uso de **variáveis de ambiente** será preferível ao invés de usar constantes, que poderá ser removido no futuro.

Para alterar o tempo de expiração há duas formas:

* por variável de ambiente (antes de instanciar):

```php
putenv('USPDEV_CACHE_EXPIRY=tempo_em_segundos');
```

* setando diretamente (depois de instanciar):

```php
$cache->expiry = tempo_em_segundos;
```

Se o tempo de expiração for 0, o cache nunca vai expirar.

Você pode definir a partir de qual tamanho de dados o cache vai ser utilizado. O valor padrão são 32 bytes para poder excluir retornos vazios e algumas mensagens de erro. Se quiser modificar esse tamanho use:

```php
putenv('USPDEV_CACHE_SMALL=tamanho_em_bytes');
```

ou

```php
$cache->small = tamanho_em_bytes;
```
