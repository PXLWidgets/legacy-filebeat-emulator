<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Source;

interface ReadableLogInterface
{

    /**
     * @return string
     */
    public function path();

    /**
     * @return int
     */
    public function pointer();

    /**
     * @return int
     */
    public function endPointer();

    /**
     * @return bool
     */
    public function hasUnprocessedLines();

}
