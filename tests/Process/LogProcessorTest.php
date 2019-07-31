<?php

namespace PXLWidgets\FilebeatEmulator\Test\Process;

use Generator;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFileStatusInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogFinderInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\LogReaderInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Transfer\LogFormatterInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Transfer\LogSenderInterface;
use PXLWidgets\FilebeatEmulator\Process\LogProcessor;
use PXLWidgets\FilebeatEmulator\Test\TestCase;
use RuntimeException;

class LogProcessorTest extends TestCase
{

    /**
     * @test
     */
    function it_processes_log_files()
    {
        $path = $this->realSourcePath('fakelogfile01.txt');

        $log       = $this->getMockLog();
        $finder    = $this->getMockFinder();
        $reader    = $this->getMockReader();
        $status    = $this->getMockStatus();
        $formatter = $this->getMockFormatter();
        $sender    = $this->getMockSender();

        $log->shouldReceive('endPointer')->andReturn(22);
        $log->shouldReceive('path')->andReturn($path);
        $log->shouldReceive('pointer')->andReturn(0);

        $finder->shouldReceive('findProcessableLogs')->once()->andReturn([ $log ]);

        $reader->shouldReceive('readLines')->with($log)->once()->andReturn($this->lineGenerator());
        $reader->shouldReceive('getCurrentPointer')->andReturnValues([7, 18, 22]);

        $formatter->shouldReceive('format')->with('test a', $log)->once()->andReturn('test a+');
        $formatter->shouldReceive('format')->with('few lines', $log)->once()->andReturn('few lines+');
        $formatter->shouldReceive('format')->with('here', $log)->once()->andReturn('here+');

        $sender->shouldReceive('send')->with('test a+')->once();
        $sender->shouldReceive('send')->with('few lines+')->once();
        $sender->shouldReceive('send')->with('here+')->once();

        $status->shouldReceive('storePointerForPath')->with($path, 22);

        $processor = new LogProcessor($finder, $reader, $status, $formatter, $sender);

        $processor->process();
    }

    /**
     * @test
     */
    function it_stores_the_file_pointer_on_processing_exception_before_rethrowing()
    {
        $path = $this->realSourcePath('fakelogfile01.txt');

        $log       = $this->getMockLog();
        $finder    = $this->getMockFinder();
        $reader    = $this->getMockReader();
        $status    = $this->getMockStatus();
        $formatter = $this->getMockFormatter();
        $sender    = $this->getMockSender();

        $log->shouldReceive('endPointer')->andReturn(22);
        $log->shouldReceive('path')->andReturn($path);
        $log->shouldReceive('pointer')->andReturn(0);

        $finder->shouldReceive('findProcessableLogs')->once()->andReturn([ $log ]);

        $reader->shouldReceive('readLines')->with($log)->once()->andReturn($this->lineGenerator());
        $reader->shouldReceive('getCurrentPointer')->andReturnValues([7, 18]);

        $status->shouldReceive('storePointerForPath')->with($path, 22);

        $formatter->shouldReceive('format')->with('test a', $log)->once()->andReturn('test a+');
        $formatter->shouldReceive('format')->with('few lines', $log)->once()->andReturn('few lines+');

        $sender->shouldReceive('send')->with('test a+')->once();
        $sender->shouldReceive('send')->with('few lines+')->andThrow(new RuntimeException('Failed on purpose'));

        $processor = new LogProcessor($finder, $reader, $status, $formatter, $sender);

        // This expectation matches the state before the exception is thrown, so AFTER the first line is processed.
        $status->shouldReceive('storePointerForPath')->with($path, 7);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed on purpose');

        $processor->process();
    }


    /**
     * @return \Mockery\MockInterface|\Mockery\Mock|ReadableLogInterface
     */
    protected function getMockLog()
    {
        return \Mockery::mock(ReadableLogInterface::class);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\Mock|LogFinderInterface
     */
    protected function getMockFinder()
    {
        return \Mockery::mock(LogFinderInterface::class);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\Mock|LogReaderInterface
     */
    protected function getMockReader()
    {
        return \Mockery::mock(LogReaderInterface::class);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\Mock|LogFileStatusInterface
     */
    protected function getMockStatus()
    {
        return \Mockery::mock(LogFileStatusInterface::class);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\Mock|LogFormatterInterface
     */
    protected function getMockFormatter()
    {
        return \Mockery::mock(LogFormatterInterface::class);
    }

    /**
     * @return \Mockery\MockInterface|\Mockery\Mock|LogSenderInterface
     */
    protected function getMockSender()
    {
        return \Mockery::mock(LogSenderInterface::class);
    }



    /**
     * @return Generator
     */
    protected function lineGenerator()
    {
        $lines = [
            'test a',
            'few lines',
            'here',
        ];

        foreach ($lines as $line) {
            yield $line;
        }
    }

}
