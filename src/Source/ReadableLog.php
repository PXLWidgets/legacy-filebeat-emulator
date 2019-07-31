<?php

namespace PXLWidgets\FilebeatEmulator\Source;

use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;

class ReadableLog implements ReadableLogInterface
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $pointer;


    /**
     * @param string $path
     * @param int    $pointer
     */
    public function __construct($path, $pointer = 0)
    {
        $this->path    = $path;
        $this->pointer = $pointer;
    }


    /**
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function pointer()
    {
        return $this->pointer;
    }

    /**
     * @return int
     */
    public function endPointer()
    {
        return (int) filesize($this->path());
    }

    /**
     * @return bool
     */
    public function hasUnprocessedLines()
    {
        return $this->endPointer() > $this->pointer();
    }

}
