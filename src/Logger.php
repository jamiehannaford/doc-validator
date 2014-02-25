<?php

namespace DocValidator;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = [])
    {
        echo PHP_EOL , strtr($message, $context) , PHP_EOL;
    }
}