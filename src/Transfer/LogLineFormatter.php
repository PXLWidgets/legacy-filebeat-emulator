<?php

namespace PXLWidgets\FilebeatEmulator\Transfer;

use PXLWidgets\FilebeatEmulator\Contracts\Config\ProcessConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Source\ReadableLogInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Transfer\LogFormatterInterface;

class LogLineFormatter implements LogFormatterInterface
{

    /**
     * @var ProcessConfigInterface
     */
    protected $config;


    public function __construct(ProcessConfigInterface $config)
    {
        $this->config = $config;
    }


    /**
     * @param string               $line
     * @param ReadableLogInterface $log
     * @return string
     */
    public function format($line, ReadableLogInterface $log)
    {
        $array = json_decode($line, true) ?: [];

        // Ensure timestamp
        if ( ! array_key_exists('timestamp', $array)) {
            $array['timestamp'] = $this->getCurrentTimestamp();
        }

        if ($this->config->index()) {
            $array['index'] = $this->config->index();
        }


        if ( ! array_key_exists('fields', $array)) {
            $array['fields'] = [];
        }

        if ($this->config->environment()) {
            $array['fields']['environment'] = $this->config->environment();
        }

        $array['fields']['source'] = $this->getSourceString();


        if ($this->config->application() !== null) {
            $array['application'] = $this->config->application();
        }


        if (count($this->config->extra())) {
            $array = array_merge($this->config->extra(), $array);
        }

        return json_encode($array);
    }

    /**
     * @return string
     */
    protected function getCurrentTimestamp()
    {
        return date('Y-m-d H:i:')
             . sprintf('%05.3f', date('s') + fmod(microtime(true), 1));
    }

    /**
     * @return string
     */
    protected function getSourceString()
    {
        return 'http_json';
    }

}
