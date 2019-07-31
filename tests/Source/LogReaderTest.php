<?php

namespace PXLWidgets\FilebeatEmulator\Test\Source;

use Generator;
use PXLWidgets\FilebeatEmulator\Exceptions\FailedToReadLogFileException;
use PXLWidgets\FilebeatEmulator\Source\LogReader;
use PXLWidgets\FilebeatEmulator\Source\ReadableLog;
use PXLWidgets\FilebeatEmulator\Test\TestCase;

/**
 * @runInSeparateProcess
 * @preserveGlobalState
 */
class LogReaderTest extends TestCase
{

    /**
     * @test
     */
    function it_reads_all_lines_from_a_file()
    {
        $reader = new LogReader();

        $generator = $reader->readLines(
            new ReadableLog($this->realSourcePath('fakelogfile01.txt'))
        );

        static::assertInstanceOf(Generator::class, $generator);

        static::assertEquals(
            [
                'Testing 1',
                'Testing 2',
                'Testing 3',
            ],
            $this->readLinesFromGenerator($generator)
        );
    }

    /**
     * @test
     */
    function it_reads_lines_from_a_file_past_a_given_pointer()
    {
        $reader = new LogReader();

        $positionAfterFirstLine = 10;

        $generator = $reader->readLines(
            new ReadableLog($this->realSourcePath('fakelogfile01.txt'), $positionAfterFirstLine)
        );

        static::assertInstanceOf(Generator::class, $generator);

        static::assertEquals(
            [
                'Testing 2',
                'Testing 3',
            ],
            $this->readLinesFromGenerator($generator)
        );
    }

    /**
     * @test
     */
    function it_silently_reads_nothing_if_pointer_is_beyond_file_size()
    {
        $reader = new LogReader();

        $generator = $reader->readLines(
            new ReadableLog($this->realSourcePath('fakelogfile01.txt'), 999)
        );

        static::assertEquals([], $this->readLinesFromGenerator($generator));
    }

    /**
     * @test
     */
    function it_throws_an_exception_when_the_file_cannot_be_opened()
    {
        $reader = new LogReader();

        $this->expectException(FailedToReadLogFileException::class);

        $generator = $reader->readLines(
            new ReadableLog($this->sourcePath('doesnotexist.txt'))
        );

        $this->readLinesFromGenerator($generator);
    }

    /**
     * @test
     */
    function it_tracks_the_current_file_pointer_while_reading_lines()
    {
        $reader = new LogReader();

        $generator = $reader->readLines(
            new ReadableLog($this->realSourcePath('fakelogfile01.txt'))
        );

        $generator->current();

        static::assertEquals(10, $reader->getCurrentPointer());

        $generator->next();

        static::assertEquals(20, $reader->getCurrentPointer());

        $generator->next();

        static::assertEquals(30, $reader->getCurrentPointer());
    }


    /**
     * @param Generator $generator
     * @return string[]
     */
    protected function readLinesFromGenerator(Generator $generator)
    {
        $lines = [];

        foreach ($generator as $line) {

            $lines[] = $line;
        }

        return $lines;
    }

}
