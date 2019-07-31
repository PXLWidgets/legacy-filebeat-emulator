<?php

namespace PXLWidgets\FilebeatEmulator\Process;

use Exception;
use PXLWidgets\FilebeatEmulator\Contracts\Process\LogProcessorInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFileStatusInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFinderInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogReaderInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Transfer\LogFormatterInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Transfer\LogSenderInterface;

class LogProcessor implements LogProcessorInterface
{

    /**
     * @var LogFinderInterface
     */
    protected $finder;

    /**
     * @var LogReaderInterface
     */
    protected $reader;

    /**
     * @var LogFileStatusInterface
     */
    protected $status;

    /**
     * @var LogFormatterInterface
     */
    protected $formatter;

    /**
     * @var LogSenderInterface
     */
    protected $sender;


    public function __construct(
        LogFinderInterface $finder,
        LogReaderInterface $reader,
        LogFileStatusInterface $status,
        LogFormatterInterface $formatter,
        LogSenderInterface $sender
    ) {
        $this->finder    = $finder;
        $this->reader    = $reader;
        $this->status    = $status;
        $this->formatter = $formatter;
        $this->sender    = $sender;
    }


    public function process()
    {
        $logs = $this->finder->findProcessableLogs();

        foreach ($logs as $log) {
            $this->processSingleLog($log);
        }
    }

    /**
     * @param ReadableLogInterface $log
     */
    protected function processSingleLog(ReadableLogInterface $log)
    {
        $previousPointer = $log->pointer();

        foreach ($this->reader->readLines($log) as $line) {

            try {
                $this->processLogLine($line, $log);

            } catch (Exception $e) {
                // Store the pointer so we know we processed the log up to this point.

                $this->status->storePointerForPath($log->path(), $previousPointer);

                throw $e;
            }

            $previousPointer = $this->reader->getCurrentPointer();
        }

        $this->status->storePointerForPath($log->path(), $log->endPointer());
    }

    /**
     * @param string               $line
     * @param ReadableLogInterface $log
     */
    protected function processLogLine($line, ReadableLogInterface $log)
    {
        $this->sender->send(
            $this->formatter->format($line, $log)
        );
    }

}
