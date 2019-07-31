<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Source;

interface LogFileStatusInterface
{

    /**
     * Returns the byte pointer for a path, 0 if no pointer is found.
     *
     * @param string $path
     * @return int
     */
    public function getPointerForPath($path);

    /**
     * Stores the byte pointer for a log's path.
     *
     * @param string $path
     * @param int    $pointer
     */
    public function storePointerForPath($path, $pointer);

}
