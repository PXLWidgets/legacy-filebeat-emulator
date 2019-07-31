<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Config;

interface SourceConfigInterface
{

    /**
     * Glob(s) indicating the files to read from
     *
     * @return string[]
     */
    public function logPaths();

    /**
     * The path where the log pointers are stored that indicate
     * how much of the logs has already been processed.
     *
     * @return string
     */
    public function logStatusFilePath();

}
