<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Config;

interface ConfigInterface
{

    /**
     * @return SourceConfigInterface
     */
    public function source();

    /**
     * @return TargetConfigInterface
     */
    public function target();

    /**
     * @return ProcessConfigInterface
     */
    public function process();

}
