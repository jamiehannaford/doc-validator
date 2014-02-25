<?php

namespace DocValidator;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    protected $stream;

    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    public function getStream()
    {
        if (!$this->stream) {
            $this->stream = defined('STDERR') ? STDERR : fopen('php://output', 'a');
        }

        return $this->stream;
    }

    public function log($level, $message, array $context = [])
    {
        $message = PHP_EOL . strtr($message, $context) . PHP_EOL;

        fwrite($this->getStream(), $message);
    }

    public function close()
    {
        if ($this->stream) {
            fclose($this->stream);
        }
    }
}