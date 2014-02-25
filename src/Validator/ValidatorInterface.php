<?php

namespace DocValidator\Validator;

use Psr\Log\LoggerInterface;

interface ValidatorInterface
{
    public function setPath($path);

    public function setContent($content);

    public function setLogger(LoggerInterface $logger);

    public function validateContent();

    public function validate(&$errorCount);

    public function outputError($errorCount);
}