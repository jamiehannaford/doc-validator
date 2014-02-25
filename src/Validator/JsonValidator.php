<?php

namespace DocValidator\Validator;

class JsonValidator extends AbstractValidator
{
    public function validateContent()
    {
        json_decode($this->content);

        /** @var false|string error */
        $this->error = $this->getLastError();
    }

    private function getLastError()
    {
        $error = false;

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
        }

        return $error;
    }

    public function outputError($errorCount)
    {
        $message  = "ERROR {count}: Malformed JSON\n\n";
        $message .= "Path: {path}\n\n";
        $message .= "Sample:\n{sample}\n\n";
        $message .= "Error: {error}\n\n";
        $message .= "{delimeter}";

        return $this->logger->error($message, [
            '{count}'     => $errorCount,
            '{path}'      => $this->path,
            '{sample}'    => $this->content,
            '{error}'     => $this->error,
            '{delimeter}' => $this->getDelimeter()
        ]);
    }
} 