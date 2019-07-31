<?php

namespace PXLWidgets\FilebeatEmulator\Contracts\Transfer;

interface LogSenderInterface
{

    /**
     * Sends a log line to the target.
     *
     * @param string $line
     */
    public function send($line);


}
