<?php

namespace PXLWidgets\FilebeatEmulator\Source;

use Exception;
use PXLWidgets\FilebeatEmulator\Contracts\Config\SourceConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFileStatusInterface;
use RuntimeException;

class LogFileStatus implements LogFileStatusInterface
{

    /**
     * @var bool
     */
    protected static $readStatus = false;

    /**
     * Mapping of byte pointer per log file path.
     *
     * @var int[]       keyed by log file path
     */
    protected static $status = [];


    /**
     * @var SourceConfigInterface
     */
    protected $config;


    public function __construct(SourceConfigInterface $config)
    {
        $this->config = $config;
    }


    /**
     * Returns the byte pointer for a path, 0 if no pointer is found.
     *
     * @param string $path
     * @return int
     */
    public function getPointerForPath($path)
    {
        $this->readStatus();

        if ( ! array_key_exists($path, static::$status)) {
            return 0;
        }

        return (int) static::$status[ $path ];
    }

    /**
     * Stores the byte pointer for a log's path.
     *
     * @param string $path
     * @param int    $pointer
     */
    public function storePointerForPath($path, $pointer)
    {
        $this->readStatus();

        static::$status[ $path ] = (int) $pointer;

        $this->writeStatus();
    }


    protected function readStatus()
    {
        if (static::$readStatus) {
            return;
        }

        $path = $this->config->logStatusFilePath();

        if ( ! file_exists($path)) {
            static::$status = [];
            return;
        }

        try {
            $json = file_get_contents($path);

        } catch (Exception $e) {

            throw new RuntimeException("Could not read log status file at '{$path}'", $e->getCode(), $e);
        }

        // Empty file is acceptable, means there is no status yet.
        if ($json === false || $json == '') {
            static::$status = [];
            return;
        }

        try {
            static::$status = json_decode($json, true);

            if (static::$status === null) {
                throw new RuntimeException('NULL json decode result');
            }

        } catch (Exception $e) {

            throw new RuntimeException(
                "Log status file contained unexpected data at '{$path}'"
                . ' (JSON error: ' . json_last_error_msg() . ')',
                $e->getCode(),
                $e
            );
        }
    }

    protected function writeStatus()
    {
        $path = $this->config->logStatusFilePath();

        try {
            file_put_contents($path, json_encode(static::$status));

        } catch (Exception $e) {

            throw new RuntimeException("Could not write log status file at '{$path}'", $e->getCode(), $e);
        }
    }

}
