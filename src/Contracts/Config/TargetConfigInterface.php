<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Config;

interface TargetConfigInterface
{

    /**
     * @return string
     */
    public function host();

    /**
     * @return bool
     */
    public function hasCredentials();

    /**
     * @return string|null
     */
    public function username();

    /**
     * @return string|null
     */
    public function password();

    /**
     * @return string[]     key-value pairs
     */
    public function headers();

}
