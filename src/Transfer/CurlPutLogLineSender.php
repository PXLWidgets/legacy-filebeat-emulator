<?php

namespace PXLWidgets\FilebeatEmulator\Transfer;

use PXLWidgets\FilebeatEmulator\Contracts\Config\TargetConfigInterface;
use PXLWidgets\FilebeatEmulator\Contracts\Transfer\LogSenderInterface;
use PXLWidgets\FilebeatEmulator\Exceptions\FailedToReadLogFileException;

class CurlPutLogLineSender implements LogSenderInterface
{

    /**
     * @var TargetConfigInterface
     */
    protected $config;


    public function __construct(TargetConfigInterface $config)
    {
        $this->config = $config;
    }


    /**
     * Sends a log line to the target.
     *
     * @param string $line
     */
    public function send($line)
    {
        $curl = curl_init($this->config->host());

        if ($this->config->hasCredentials()) {
            curl_setopt($curl, CURLOPT_USERPWD, "{$this->config->username()}:{$this->config->password()}");
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $line);

        if (count($this->config->headers())) {
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->formatHeadersForCurl($this->config->headers()));
        }

        $response = curl_exec($curl);

        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            throw new FailedToReadLogFileException("Failed to send to '{$this->config->host()}': {$error}");
        }

        if ($this->isUnexpectedResponse($response)) {
            throw new FailedToReadLogFileException(
                "Unexpected response from server '{$this->config->host()}': {$response}"
            );
        }
    }


    protected function isUnexpectedResponse($response)
    {
        return strtolower(trim($response)) !== 'ok';
    }

    /**
     * @param array $headers
     * @return array
     */
    protected function formatHeadersForCurl(array $headers)
    {
        $formatted = [];

        foreach ($headers as $key => $value) {

            if (is_numeric($key)) {
                $formatted[] = $value;
            } else {
                $formatted[] = $key . ': ' . $value;
            }
        }

        return $formatted;
    }

}
