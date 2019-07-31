<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Source;

interface LogFinderInterface
{

    /**
     * Returns an array with paths to log files that may be read.
     *
     * @return ReadableLogInterface[]
     */
    public function findProcessableLogs();

}
