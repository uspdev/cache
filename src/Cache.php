<?php
namespace Uspdev\Cache;

class Cache
{
    // default value for expiry
    public $expiry;
    public $small;
    public $disable;

    // public function __construct(object $classToBeCached = null)
    // esta construção acima é a partir do PHP 7.2
    // por enquanto vamos manter compatibilidade com php 7.0 - Masaki 11/2019
    public function __construct($classToBeCached = null)
    {
        // vamos injetar a classe que queremos cachear
        $this->cachedClass = $classToBeCached;

        // vamos verificar se o cache está desativado nas constantes ou no ambiente
        $this->disable = false;
        $this->disable = defined('USPDEV_CACHE_DISABLE') ? USPDEV_CACHE_DISABLE : $this->disable;
        $this->disable = getenv('USPDEV_CACHE_DISABLE') ? getenv('USPDEV_CACHE_DISABLE') : $this->disable;

        // o tempo de expiração pode ser definido por constante, ambiente
        // ou setado sob demanda
        $this->expiry = 4 * 60 * 60; // Valor padrão: 4 horas
        $this->expiry = defined('USPDEV_CACHE_EXPIRY') ? USPDEV_CACHE_EXPIRY : $this->expiry;
        $this->expiry = getenv('USPDEV_CACHE_EXPIRY') ? intval(getenv('USPDEV_CACHE_EXPIRY')) : $this->expiry;

        // vamos definir a partir de qual tamanho de dado vamos cachear
        // isso para não cachear null, vazio, etc
        // para nao cachear mensagens de erro, pode ser necessário aumentar um pouco
        $this->small = 32; // Valor padrão: 32 bytes
        $this->small = defined('USPDEV_CACHE_SMALL') ? USPDEV_CACHE_SMALL : $this->small;
        $this->small = getenv('USPDEV_CACHE_SMALL') ? intval(getenv('USPDEV_CACHE_SMALL')) : $this->small;

        if ($this->disable) {
            // se desativado, nao procuraremos o servidor memcached
        } else {
            // vamos conectar o servidor memcached local
            $cache = new \Memcached();
            if (empty($cache->getServerList())) {
                $cache->addServer('127.0.0.1', 11211);
            }
            if (!$cache->getVersion()) {
                die('sem memcached');
            }
            $this->cache = $cache;
        }
    }

    public function getCached(string $cachedMethod, $param = null, $key = null)
    {
        // se o cache estiver desativado vamos ignorar a parte de cache e retornar dados brutos
        if ($this->disable) {
            return $this->getRaw($cachedMethod, $param);
        }

        // se cache estiver ativado, vamos criar a chave
        $this->setCacheKey($cachedMethod, $key ?? $param);

        // e verificar se o dado está no cache
        $data = $this->cache->get($this->cacheKey);
        $this->cacheStatus = $this->cache->getResultCode();

        // se não está no cache ou está expirado, vamos buscar na classe e colocar no cache
        if ($this->cacheStatus != \Memcached::RES_SUCCESS) {
            $data = $this->getRaw($cachedMethod, $param);

            // não vamos cachear dados pequenos
            if (strlen(serialize($data)) > $this->small) {
                $this->setCacheData($data); // o $this->cacheKey deve estar previamentte criado
            }
        }

        return $data;
    }

    public function getRaw(string $cachedMethod, $param)
    {
        // O param pode ser string ou array de strings.
        // Se for string vamos converter para array e usar unpack na chamada do método
        $param = is_array($param) ? $param : [$param];

        // vamos pegar os dados sem verificar o cache
        if (strpos($cachedMethod, '::')) {
            // estático
            $data = $param ? $cachedMethod(...$param) : $cachedMethod();
        } else {
            // instanciado
            $data = $param ? $this->cachedClass->$cachedMethod(...$param) : $this->cachedClass->$cachedMethod();
        }

        return $data;
    }

    public function status()
    {
        $ret['expiry'] = $this->expiry;
        $ret['smallData'] = $this->small;
        $ret['disable'] = $this->disable ? 'sim' : 'nao';
        $ret['version'] = 'não disponível';
        if (!$this->disable) {
            $stats = $this->cache->getStats();
            $stats = array_pop($stats);
            $ret['curr_items'] = $stats['curr_items'];
            $ret['get_hits'] = $stats['get_hits'];
            $ret['get_misses'] = $stats['get_misses'];
            $ret['version'] = $stats['version'];
        }

        return $ret;
    }

    public function flush()
    {
        if (!$this->disable) {
            if ($this->cache->flush()) {
                return true;
            } else {
                echo 'Erro na limpeza do cache: ', $this->cache->getResultCode();
                echo $this->cache->getResultMessage(), PHP_EOL;
                exit;
            }
        }
        return true;
    }

    private function setCacheData($data)
    {
        // Vamos colocar no cache
        $this->cache->set($this->cacheKey, $data, $this->expiry);
        $this->cacheStatus = $this->cache->getResultCode();
    }

    private function setCacheKey(string $cachedMethod, $param)
    {
        // vamos criar uma chave adequada dependente dos parametros
        $paramString = serialize($param);

        if (empty($this->cachedClass)) {
            $this->cacheKey = md5($cachedMethod . '-' . $paramString);
        } else {
            $this->cacheKey = md5(get_class($this->cachedClass) . '-' . $cachedMethod . '-' . $paramString);
        }
    }
}
