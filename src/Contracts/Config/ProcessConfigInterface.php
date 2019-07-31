<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Config;

interface ProcessConfigInterface
{

    /**
     * @return string
     */
    public function index();

    /**
     * @return string|null
     */
    public function environment();

    /**
     * @return string|null
     */
    public function application();

    /**
     * @return array
     */
    public function extra();


}
