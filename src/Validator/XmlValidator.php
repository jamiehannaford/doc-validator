<?php

namespace DocValidator\Validator;

use DocValidator\Logger;

class XmlValidator extends AbstractValidator
{
    public function validateContent()
    {
        if (false === @simplexml_load_string($this->content)) {
            $this->error = true;
        }
    }

    public function outputError($errorCount)
    {
        $message  = "ERROR {error}: Malformed XML\n\n";
        $message .= "Path: {path}\n\n";
        $message .= "Sample:\n{sample}\n\n";
        $message .= "{delimeter}";

        $this->logger->error($message, [
            '{error}'     => $errorCount,
            '{path}'      => $this->path,
            '{sample}'    => $this->content,
            '{delimeter}' => $this->getDelimeter()
        ]);
    }
}