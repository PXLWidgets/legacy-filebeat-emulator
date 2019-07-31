<?php

namespace PXLWidgets\FilebeatEmulator\Test;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $path
     * @return string
     */
    protected function sourcePath($path = '')
    {
        return __DIR__ . '/resources/source/' . $path;
    }

    /**
     * @param string $path
     * @return bool|string
     */
    protected function realSourcePath($path)
    {
        return realpath($this->sourcePath($path));
    }

}
