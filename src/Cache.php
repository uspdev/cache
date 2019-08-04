<?php

class cache
{

    public function __construct(object $ClassToBeCached)
    {
        $this->cache = new \Memcached();
        if (empty($this->cache->getServerList())) {
            $this->cache->addServer('127.0.0.1', 11211);
        }
        $this->longExpiry = 4 * 60 * 60; // expira em 4 horas

        // vamos injetar a chasse que queremos cachear
        $this->cachedClass = $ClassToBeCached;
    }

    public function getCached(string $cachedMethod, $param = null)
    {
        // vamos criar uma chave adequada dependente dos parametros
        $paramString = is_array($param) ? implode('-', $param) : $param;
        $cacheKey = get_class($this->cachedClass) . '-' . $cachedMethod . '-' . $paramString;

        $data = $this->cache->get($cacheKey);
        $this->cacheStatus = $this->cache->getResultCode();

        if ($this->cacheStatus != Memcached::RES_SUCCESS) {
            // não está no cache, vamos buscar na classe e colocar no cache
            $data = $this->getRaw($cachedMethod, $param);
        }
        
        return $data;
    }

    public function getRaw(string $cachedMethod, $param = null)
    {
        // vamos pegar os dados sem verificar o cache
        if ($param) {
            $data = $this->cachedClass->$cachedMethod($param);
        } else {
            $data = $this->cachedClass->$cachedMethod();
        }

        // vamos criar uma chave adequada dependente dos parametros
        $paramString = is_array($param) ? implode('-', $param) : $param;
        $cacheKey = get_class($this->cachedClass) . '-' . $cachedMethod . '-' . $paramString;

        // e vamos colocar no cache
        $this->cache->set($cacheKey, $data, $this->longExpiry);
        $this->cacheStatus = $this->cache->getResultCode();

        return $data;

    }
}
