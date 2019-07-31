<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Source;

use Generator;

interface LogReaderInterface
{

    /**
     * @param ReadableLogInterface $log
     * @return Generator|string[]
     */
    public function readLines(ReadableLogInterface $log);

    /**
     * Returns the pointer for currently being read.
     *
     * @return int
     */
    public function getCurrentPointer();

}
