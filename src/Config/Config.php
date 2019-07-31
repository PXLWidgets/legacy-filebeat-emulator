<?php

namespace PXLWidgets\FilebeatEmulator\Config;

use PXLWidgets\FilebeatEmulator\Contracts\Config\ConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Config\ProcessConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Config\SourceConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Config\TargetConfigInterface;

class Config implements ConfigInterface
{

    /**
     * @var SourceConfigInterface
     */
    protected $source;

    /**
     * @var TargetConfigInterface
     */
    protected $target;

    /**
     * @var ProcessConfigInterface
     */
    protected $process;


    public function __construct(
        SourceConfigInterface $source,
        TargetConfigInterface $target,
        ProcessConfigInterface $process
    ) {
        $this->target  = $target;
        $this->process = $process;
        $this->source = $source;
    }

    /**
     * @return SourceConfigInterface
     */
    public function source()
    {
        return $this->source;
    }

    /**
     * @return TargetConfigInterface
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * @return ProcessConfigInterface
     */
    public function process()
    {
        return $this->process;
    }

}
