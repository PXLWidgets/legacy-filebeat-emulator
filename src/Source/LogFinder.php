<?php

namespace PXLWidgets\FilebeatEmulator\Source;

use PXLWidgets\FilebeatEmulator\Contracts\Config\SourceConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFileStatusInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFinderInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;

class LogFinder implements LogFinderInterface
{

    /**
     * @var SourceConfigInterface
     */
    protected $config;

    /**
     * @var LogFileStatusInterface
     */
    protected $status;


    public function __construct(SourceConfigInterface $config, LogFileStatusInterface $status)
    {
        $this->config = $config;
        $this->status = $status;
    }


    /**
     * Returns an array with paths to log files that may be read.
     *
     * @return ReadableLogInterface[]
     */
    public function findProcessableLogs()
    {
        $files = [];

        foreach ($this->config->logPaths() as $glob) {

            $files = array_merge(
                $files,
                $this->filterFilesByStatus(
                    $this->findFilesForGlob($glob)
                )
            );
        }

        return $files;
    }

    /**
     * @param string $glob
     * @return ReadableLogInterface[]
     */
    protected function findFilesForGlob($glob)
    {
        $files = glob($glob);

        return array_map(
            [$this, 'castPathToReadableLogInstance'],
            array_filter(
                array_map(
                    function ($path) {
                        return realpath($path);
                    },
                    $files
                )
            )
        );
    }

    /**
     * @param ReadableLogInterface[] $paths
     * @return ReadableLogInterface[]
     */
    protected function filterFilesByStatus(array $paths)
    {
        return array_filter(
            $paths,
            function (ReadableLogInterface $log) {
                return $log->hasUnprocessedLines();
            }
        );
    }

    /**
     * @param string $path
     * @return ReadableLogInterface
     */
    protected function castPathToReadableLogInstance($path)
    {
        return new ReadableLog($path, $this->status->getPointerForPath($path));
    }

}
