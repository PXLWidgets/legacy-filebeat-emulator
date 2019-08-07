<?php

namespace PXLWidgets\FilebeatEmulator\Test\Process;

use PXLWidgets\FilebeatEmulator\Config\ProcessConfig;
use PXLWidgets\FilebeatEmulator\Source\ReadableLog;
use PXLWidgets\FilebeatEmulator\Test\TestCase;
use PXLWidgets\FilebeatEmulator\Transfer\LogLineFormatter;

class LogLineFormatterTest extends TestCase
{

    /**
     * @test
     */
    function it_formats_a_json_encoded_log_line()
    {
        $config = new ProcessConfig(
            'testing-index',
            'testenv',
            'testapplication',
            [
                'some' => 'extra data',
            ]
        );

        $log = new ReadableLog('testing/tmp.txt', 0);

        $line = json_encode([
            'timestamp'  => '2019-07-30 13:25:59.031',
            'channel'    => 'testing',
            'category'   => 'more testing',
            'message'    => 'log line text here',
            'severity'   => 'DEBUG',
            'level'      => 100,
        ]);

        $formatter = new LogLineFormatter($config);

        $output = $formatter->format($line, $log);

        static::assertJson($output);

        $array = json_decode($output, true);

        static::assertArrayHasKey('timestamp', $array);
        static::assertEquals('2019-07-30 13:25:59.031', $array['timestamp']);

        static::assertArrayHasKey('some', $array, 'Extra data was not added');
        static::assertEquals('extra data', $array['some']);

        static::assertArrayHasKey('index', $array, 'Index was not set');
        static::assertEquals('testing-index', $array['index']);

        static::assertArrayHasKey('environment', $array['fields']);
        static::assertEquals('testenv', $array['fields']['environment']);

        static::assertArrayHasKey('source', $array['fields']);
        static::assertEquals('http_json', $array['fields']['source']);

        static::assertEquals('testapplication', $array['application']);
        static::assertArrayHasKey('application', $array, 'Application was not set');
    }

    /**
     * @test
     */
    function it_adds_a_timestamp_if_none_is_set()
    {
        $config = new ProcessConfig(
            'testing-index',
            'testenv',
            null
        );

        $log = new ReadableLog('testing/tmp.txt', 0);

        $line = json_encode([
            'message' => 'log line text here',
        ]);

        $formatter = new LogLineFormatter($config);

        $output = $formatter->format($line, $log);

        static::assertJson($output);

        $array = json_decode($output, true);

        static::assertArrayHasKey('timestamp', $array);
        static::assertRegExp('#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{3}$#', $array['timestamp']);
    }

}
