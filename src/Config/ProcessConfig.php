<?php

namespace PXLWidgets\FilebeatEmulator\Config;

use PXLWidgets\FilebeatEmulator\Contracts\Config\ProcessConfigInterface;

class ProcessConfig implements ProcessConfigInterface
{

    /**
     * @var string
     */
    protected $index;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string|null
     */
    protected $application;

    /**
     * @var array
     */
    protected $extra;


    public function __construct($index, $environment, $application, array $extra = [])
    {
        $this->index       = $index;
        $this->environment = $environment;
        $this->application = $application;
        $this->extra       = $extra;
    }


    /**
     * @return string
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function environment()
    {
        return $this->environment;
    }

    /**
     * @return string|null
     */
    public function application()
    {
        return $this->application;
    }

    /**
     * @return array
     */
    public function extra()
    {
        return $this->extra;
    }

}
