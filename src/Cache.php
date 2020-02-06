<?php
namespace Uspdev\Cache;

class Cache
{
    // default value for expiry
    public $expiry;
    public $smallData;

    // public function __construct(object $classToBeCached = null)
    // esta construção acima é a partir do PHP 7.2
    // por enquanto vamos manter compatibilidade com php 7.0 - Masaki 11/2019
    public function __construct($classToBeCached = null)
    {
        // vamos injetar a chasse que queremos cachear
        $this->cachedClass = $classToBeCached;

        if (defined('USPDEV_CACHE_DISABLE') and USPDEV_CACHE_DISABLE) {
            // nao procuraremos o memcached
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

        // o tempo de expiração pode ser definido por constante 
        // ou setado sob demanda
        if (defined('USPDEV_CACHE_EXPIRY')) {
            $this->expiry = USPDEV_CACHE_EXPIRY;
        } else {
            $this->expiry = 4 * 60 * 60; // Valor padrão: 4 horas
        }

        // vamos definir a partir de qual tamanho de dado vamos cachear
        // isso para não cachear null, vazio, etc
        // para nao cachear mensagens de erro, pode ser necessário aumentar um pouco
        if (defined('USPDEV_CACHE_SMALL')) {
            $this->smallData = USPDEV_CACHE_SMALL;
        } else {
            $this->smallData = 32 ; // Valor padrão: 32 bytes
        }
    }

    public function getCached(string $cachedMethod, $param = null)
    {
        // se o cache estiver desativado vamos ignorar a parte de cache e retornar dados brutos
        if (defined('USPDEV_CACHE_DISABLE') and USPDEV_CACHE_DISABLE) {
            return $this->getRaw($cachedMethod, $param);
        }

        // se cache estiver ativado, vamos criar a chave
        $this->setCacheKey($cachedMethod, $param);

        // e verificar se o dado está no cache
        $data = $this->cache->get($this->cacheKey);
        $this->cacheStatus = $this->cache->getResultCode();

        // se não está no cache ou está expirado, vamos buscar na classe e colocar no cache
        if ($this->cacheStatus != \Memcached::RES_SUCCESS) {
            $data = $this->getRaw($cachedMethod, $param);

            // não vamos cachear dados pequenos
            if (strlen(serialize($data)) > $this->smallData) {
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
            $this->cacheKey = $cachedMethod . '-' . $paramString;
        } else {
            $this->cacheKey = get_class($this->cachedClass) . '-' . $cachedMethod . '-' . $paramString;
        }
    }
}
