<?php

/**
 * We are built to run in Azure Containers, Env vars containing all sorts of secrets
 * will be stored via the container, pulled directly from a keystore and returned here.
 * 
 */
class EnvStore
{

    private $key = "";

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * We get the local environment variable, meaning only the value set by the environment and
     * not those changed by putenv/SAPI
     * @return array|bool|string
     */
    public function getValue()
    {
        return getenv($this->key, true);
    }
}
