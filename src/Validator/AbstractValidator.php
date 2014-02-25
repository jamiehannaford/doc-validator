<?php

namespace DocValidator\Validator;

use Psr\Log\LoggerAwareTrait;

abstract class AbstractValidator implements ValidatorInterface
{
    use LoggerAwareTrait;

    protected $content;
    protected $path;
    protected $errorCount;
    protected $error;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setErrorCount(&$count)
    {
        $this->errorCount = $count;
    }

    public function validate(&$errorCount)
    {
        $this->validateContent();

        if ($this->error) {
            $this->outputError(++$errorCount);
        }
    }

    public function getDelimeter()
    {
        return str_repeat('=', 50);
    }
}