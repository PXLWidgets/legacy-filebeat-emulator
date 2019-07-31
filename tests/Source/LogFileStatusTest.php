<?php

namespace PXLWidgets\FilebeatEmulator\Test\Source;

use PXLWidgets\FilebeatEmulator\Config\SourceConfig;
use PXLWidgets\FilebeatEmulator\Source\LogFileStatus;
use PXLWidgets\FilebeatEmulator\Test\TestCase;

/**
 * @runInSeparateProcess
 * @preserveGlobalState
 */
class LogFileStatusTest extends TestCase
{

    /**
     * @test
     */
    function it_returns_zero_for_unknown_file_path_pointer()
    {
        $this->clearStatus();

        $config = new SourceConfig([], $this->getTempStatusPath());

        $status = new LogFileStatus($config);

        static::assertSame(0, $status->getPointerForPath('does-not-exist/file.txt'));
    }

    /**
     * @test
     */
    function it_returns_the_pointer_value_for_a_known_path()
    {
        $this->setPointerForPath('some-known-path/file.txt', 15);

        $config = new SourceConfig([], $this->getTempStatusPath());

        $status = new LogFileStatus($config);

        static::assertSame(15, $status->getPointerForPath('some-known-path/file.txt'));
    }

    /**
     * @test
     */
    function it_stores_a_pointer_for_a_path()
    {
        $this->clearStatus();

        $path = 'some-path-saved/file.txt';

        $config = new SourceConfig([], $this->getTempStatusPath());

        $status = new LogFileStatus($config);

        static::assertSame(0, $status->getPointerForPath($path), 'Should not have a value before storing');

        $status->storePointerForPath($path, 56);

        static::assertSame(56, $status->getPointerForPath($path), 'Should have correct value after storing');
    }


    protected function clearStatus()
    {
        if ( ! file_exists($this->getTempStatusPath())) {
            return;
        }

        unlink($this->getTempStatusPath());
    }

    protected function getTempStatusPath()
    {
        return $this->sourcePath('tmp-status.txt');
    }

    /**
     * @param string $path
     * @param int    $pointer
     */
    protected function setPointerForPath($path, $pointer)
    {
        $status = [
            $path => $pointer,
        ];

        $this->clearStatus();

        file_put_contents($this->getTempStatusPath(), json_encode($status));
    }

}
