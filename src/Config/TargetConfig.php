<?php

namespace PXLWidgets\FilebeatEmulator\Config;

use PXLWidgets\FilebeatEmulator\Contracts\Config\TargetConfigInterface;

class TargetConfig implements TargetConfigInterface
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var array
     */
    protected $headers;


    public function __construct($host, $username = null, $password = null, array $headers = [])
    {
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
        $this->headers  = $headers;
    }


    /**
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * @return bool
     */
    public function hasCredentials()
    {
        return $this->username !== null && $this->password !== null;
    }

    /**
     * @return string|null
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @return string[]     key-value pairs
     */
    public function headers()
    {
        return $this->headers;
    }

}
