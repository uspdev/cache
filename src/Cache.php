<?php
namespace Uspdev\cache;

class Cache
{
    // default values for expiry
    public $longExpiry = 4 * 60 * 60; // expiry in 4 hours
    public $shortExpiry = 10 * 60; //10 minutes

    public function __construct(object $classToBeCached = null)
    {
        // vamos conectar o servidor memcached local
        $cache = new \Memcached();
        if (empty($cache->getServerList())) {
            $cache->addServer('127.0.0.1', 11211);
        }
        if (!$cache->getVersion()) {
            die('sem memcached');
        }
        $this->cache = $cache;

        // vamos injetar a chasse que queremos cachear
        $this->cachedClass = $classToBeCached;
    }

    public function getCached(string $cachedMethod, $param = null)
    {
        // criar chave
        $this->setCacheKey($cachedMethod, $param);

        // verifica o cache
        $data = $this->cache->get($this->cacheKey);
        $this->cacheStatus = $this->cache->getResultCode();

        if ($this->cacheStatus != \Memcached::RES_SUCCESS) {
            // não está no cache ou está expirado, vamos buscar na classe e colocar no cache
            $data = $this->getRaw($cachedMethod, $param);
        }

        return $data;
    }

    public function getRaw(string $cachedMethod, $param = null)
    {
        // vamos pegar os dados sem verificar o cache
        if (strpos($cachedMethod, '::')) {
            // estático
            $data = $param ? $cachedMethod($param) : $cachedMethod();
        } else {
            // instanciado
            $data = $param ? $this->cachedClass->$cachedMethod($param) : $this->cachedClass->$cachedMethod();
        }

        // criar chave
        $this->setCacheKey($cachedMethod, $param);

        // e vamos colocar no cache
        $this->cache->set($this->cacheKey, $data, $this->longExpiry);
        $this->cacheStatus = $this->cache->getResultCode();

        return $data;
    }

    private function setCacheKey(string $cachedMethod, $param = null)
    {
        // vamos criar uma chave adequada dependente dos parametros
        $paramString = is_array($param) ? implode('-', $param) : $param;

        if (empty($this->cachedClass)) {
            $this->cacheKey = $cachedMethod . '-' . $paramString;
        } else {
            $this->cacheKey = get_class($this->cachedClass) . '-' . $cachedMethod . '-' . $paramString;
        }
    }
}
