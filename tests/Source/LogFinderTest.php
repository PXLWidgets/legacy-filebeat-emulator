<?php

namespace PXLWidgets\FilebeatEmulator\Test\Source;

use PXLWidgets\FilebeatEmulator\Config\SourceConfig;
use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;
use PXLWidgets\FilebeatEmulator\Source\LogFileStatus;
use PXLWidgets\FilebeatEmulator\Source\LogFinder;
use PXLWidgets\FilebeatEmulator\Test\TestCase;

class LogFinderTest extends TestCase
{

    /**
     * @test
     */
    function it_finds_files_by_glob_pattern()
    {
        $this->clearStatus();

        $config = $this->getStandardConfig();

        $status = new LogFileStatus($config);

        $finder = new LogFinder($config, $status);

        $logs = $finder->findProcessableLogs();

        static::assertCount(3, $logs);

        static::assertInstanceOf(ReadableLogInterface::class, $logs[0]);
        static::assertEquals($this->realSourcePath('fakelogfile01.txt'), $logs[0]->path());

        static::assertInstanceOf(ReadableLogInterface::class, $logs[1]);
        static::assertEquals($this->realSourcePath('fakelogfile02.txt'), $logs[1]->path());

        static::assertInstanceOf(ReadableLogInterface::class, $logs[2]);
        static::assertEquals($this->realSourcePath('fakelogfile03.txt'), $logs[2]->path());
    }

    /**
     * @test
     */
    function it_finds_only_files_that_are_not_excluded_by_pointer()
    {
        $this->setStatusToExcludeAFile();

        $config = $this->getStandardConfig();

        $status = new LogFileStatus($config);

        $finder = new LogFinder($config, $status);

        $logs = $finder->findProcessableLogs();

        static::assertCount(2, $logs);

        static::assertInstanceOf(ReadableLogInterface::class, $logs[0]);
        static::assertEquals($this->realSourcePath('fakelogfile01.txt'), $logs[0]->path());

        static::assertInstanceOf(ReadableLogInterface::class, $logs[1]);
        static::assertEquals($this->realSourcePath('fakelogfile03.txt'), $logs[1]->path());
    }


    protected function clearStatus()
    {
        file_put_contents($this->realSourcePath('status.txt'), '');
    }


    protected function setStatusToExcludeAFile()
    {
        $excludedFile    = $this->realSourcePath('fakelogfile02.txt');
        $notExcludedFile = $this->realSourcePath('fakelogfile03.txt');

        $excludePointer    = filesize($excludedFile);
        $notExcludePointer = filesize($notExcludedFile) - 5;

        file_put_contents(
            $this->realSourcePath('status.txt'),
            json_encode([
                $excludedFile    => $excludePointer,
                $notExcludedFile => $notExcludePointer,
            ])
        );
    }

    protected function getStandardConfig()
    {
        return new SourceConfig(
            [
                $this->sourcePath('fakelogfile*.txt')
            ],
            $this->realSourcePath('status.txt')
        );
    }

}
