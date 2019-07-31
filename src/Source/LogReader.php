<?php

namespace PXLWidgets\FilebeatEmulator\Source;

use Exception;
use Generator;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogReaderInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;
use PXLWidgets\FilebeatEmulator\Exceptions\FailedToReadLogFileException;

class LogReader implements LogReaderInterface
{

    /**
     * Maximum log row size.
     *
     * @var int
     */
    const BUFFER_SIZE = 16384;

    /**
     * @var int
     */
    protected $currentPointer = 0;


    /**
     * @param ReadableLogInterface $log
     * @return Generator|string[]
     */
    public function readLines(ReadableLogInterface $log)
    {
        $this->currentPointer = 0;

        try {
            $handle = @fopen($log->path(), 'r');

        } catch (Exception $e) {
            throw new FailedToReadLogFileException(
                "Failed to open file '{$log->path()}' for reading",
                $e->getCode(),
                $e
            );
        }

        if ( ! $handle) {
            throw new FailedToReadLogFileException(
                "Could not open file '{$log->path()}' for reading from pointer {$log->pointer()}"
            );
        }

        if ( ! $log->hasUnprocessedLines()) {
            return;
        }

        if ($log->pointer() > 0) {
            @fseek($handle, $log->pointer());
        }


        // Loop through the lines of the file
        while (($buffer = fgets($handle, static::BUFFER_SIZE)) !== false) {

            $this->currentPointer = ftell($handle);

            yield trim($buffer);
        }

        if ( ! feof($handle)) {
            throw new FailedToReadLogFileException(
                "Failed fgets() reading from '{$log->path()}' from pointer {$log->pointer()}"
            );
        }

        fclose($handle);
    }

    /**
     * Returns the pointer for currently being read.
     *
     * @return int
     */
    public function getCurrentPointer()
    {
        return $this->currentPointer;
    }

}
