<?php

namespace DocValidator\Validator;

use Psr\Log\LoggerInterface;

class ValidatorFactory
{
    public static function validate(LoggerInterface $logger, $string, $path, &$count)
    {
        // decode any ASCII chars
        $value = trim(html_entity_decode($string));

        if (self::isXml($value)) {
            $validator = new XmlValidator();
        } elseif (self::isJson($value)) {
            $validator = new JsonValidator();
        } else {
            return false;
        }

        $validator->setContent($value);
        $validator->setPath($path);
        $validator->setLogger($logger);

        $validator->validate($count);
    }

    protected static function isJson($string)
    {
        return self::startsWith($string, '{');
    }

    protected static function isXml($string)
    {
        return self::startsWith($string, '<?xml');
    }

    protected static function startsWith($string, $keyword)
    {
        return $string[0] == $keyword;
    }
}