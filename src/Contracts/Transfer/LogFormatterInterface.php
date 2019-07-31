<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Transfer;

use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;

interface LogFormatterInterface
{

    /**
     * @param string               $line
     * @param ReadableLogInterface $log
     * @return string
     */
    public function format($line, ReadableLogInterface $log);

}
