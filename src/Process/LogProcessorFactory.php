<?php

namespace PXLWidgets\FilebeatEmulator\Process;

use PXLWidgets\FilebeatEmulator\Contracts\Config\ConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Config\SourceConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Process\LogProcessorInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFileStatusInterface;
use PXLWidgets\FilebeatEmulator\Source\LogFileStatus;
use PXLWidgets\FilebeatEmulator\Source\LogFinder;
use PXLWidgets\FilebeatEmulator\Source\LogReader;
use PXLWidgets\FilebeatEmulator\Transfer\CurlPutLogLineSender;
use PXLWidgets\FilebeatEmulator\Transfer\LogLineFormatter;

class LogProcessorFactory
{

    /**
     * @param ConfigInterface $config
     * @return LogProcessorInterface
     */
    public function make(ConfigInterface $config)
    {
        $status = $this->makeStatus($config->source());

        return new LogProcessor(
            new LogFinder($config->source(), $status),
            new LogReader(),
            $status,
            new LogLineFormatter($config->process()),
            new CurlPutLogLineSender($config->target())
        );
    }

    /**
     * @param SourceConfigInterface $config
     * @return LogFileStatusInterface
     */
    protected function makeStatus(SourceConfigInterface $config)
    {
        return new LogFileStatus($config);
    }

}
