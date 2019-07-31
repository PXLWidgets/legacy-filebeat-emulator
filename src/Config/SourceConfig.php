<?php

namespace PXLWidgets\FilebeatEmulator\Config;

use PXLWidgets\FilebeatEmulator\Contracts\Config\SourceConfigInterface;

class SourceConfig implements SourceConfigInterface
{


    /**
     * @var string[]
     */
    protected $paths;

    /**
     * @var string
     */
    protected $statusFilePath;


    public function __construct(array $paths, $statusFilePath)
    {
        $this->paths          = $paths;
        $this->statusFilePath = $statusFilePath;
    }


    /**
     * Glob(s) indicating the files to read from
     *
     * @return string[]
     */
    public function logPaths()
    {
        return $this->paths;
    }

    /**
     * The path where the log pointers are stored that indicate
     * how much of the logs has already been processed.
     *
     * @return string
     */
    public function logStatusFilePath()
    {
        return $this->statusFilePath;
    }

}
