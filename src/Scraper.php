<?php

namespace DocValidator;

use DocValidator\Validator\ValidatorFactory;
use RecursiveDirectoryIterator as RecursiveDirectory;
use RecursiveIteratorIterator as RecursiveIterator;

class Scraper 
{
    const DEFAULT_URI = 'docs.rackspace.com';

    private $baseUri = self::DEFAULT_URI;
    private $count = 0;
    private $options = [];
    private $startTime;

    public static function run()
    {
        return (new self())->execute();
    }

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function execute()
    {
        $this->startTime = microtime(true);

        $this->processArgs();
        $this->downloadPaths();

        $iterator = new RecursiveIterator(
            new RecursiveDirectory(
                $this->getDocsPath(),
                RecursiveDirectory::SKIP_DOTS
            ),
            RecursiveIterator::SELF_FIRST,
            RecursiveIterator::CATCH_GET_CHILD
        );

        $this->traverseDirectory($iterator);

        $this->cleanup();
    }

    public function setBaseUri($uri)
    {
        $this->baseUri = $uri ?: self::DEFAULT_URI;
    }

    public function setSkipWget($bool)
    {
        $this->skipWget = (bool) $bool;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    protected function processArgs()
    {
        $short = "u::s::q::l::";
        $long  = [
            'uri::',
            'skip-wget::',
            'quiet-wget::',
            'log-file::'
        ];

        $options = getopt($short, $long);

        foreach ($options as $key => $val) {
            switch ($key) {
                case 'u':
                case 'uri':
                    $this->setBaseUri($val);
                    break;
                case 's':
                case 'skip-wget':
                    $this->setOption('skip-wget', true);
                    break;
                case 'q':
                case 'quiet-wget':
                    $this->setOption('quiet-wget', true);
                    break;
                case 'l':
                case 'log-file':
                    $this->logger->setStream($this->getOutputStream($val));
                    break;
            }
        }
    }

    protected function downloadPaths()
    {
        if ($this->getOption('skip-wget') === true) {
            return;
        }

        $wgetPath = $this->getWgetPrefix();

        if (!file_exists($wgetPath)) {
            mkdir($wgetPath, 0777, true);
        }

        $command = 'wget';
        $command .= ' --recursive';
        $command .= ' --accept html';
        $command .= ' --domains ' . $this->formatDirUri($this->baseUri);
        $command .= ' --no-parent';
        $command .= " --directory-prefix $wgetPath";
        $command .= ' --output-file /dev/null';

        if ($this->getOption('quiet-wget')) {
            $command .= ' --quiet';
        }

        $command .= " $this->baseUri";

        shell_exec($command);
    }

    protected function getDocsPath()
    {
        return $this->getWgetPrefix() . $this->formatDirUri($this->baseUri);
    }

    protected function getWgetPrefix()
    {
        return __DIR__ . '/../docs/';
    }

    protected function formatDirUri($string)
    {
        return trim(preg_replace('#https?://#', '', $string));
    }

    protected function traverseDirectory($directory)
    {
        foreach ($directory as $path => $file) {
            if ($file->isFile()) {
                $this->scanFile($path);
            }
        }
    }

    protected function scanFile($path)
    {
        $content = file_get_contents($path);
        $matches = [];

        preg_match_all(
            '/<pre [^>]*?class="programlisting .*?">([\\s\\S]*?)<\/pre>/',
            $content,
            $matches
        );

        if (isset($matches[1]) && is_array($matches[1])) {
            foreach ($matches[1] as $value) {
                ValidatorFactory::validate($this->logger, $value, $path, $this->count);
            }
        }
    }

    protected function formatDuration()
    {
        $duration = microtime(true) - $this->startTime;

        $string = '';

        if (($minutes = floor($duration / 60)) > 0) {
            $string .= "$minutes minute" . (($minutes > 1) ? 's' : '') . ' ';
        }

        if (($seconds = $duration % 60) > 0) {
            if ($minutes > 0) {
                $string .= "and ";
            }
            $string .= "$seconds seconds";
        }

        return $string ?: number_format($duration, 2) . ' seconds';
    }

    protected function cleanup()
    {
        $this->logger->info('Process finished. Time taken: {time}. {count} errors found.', [
            '{count}' => $this->count,
            '{time}'  => $this->formatDuration()
        ]);

        $this->logger->close();
    }

    protected function getOutputStream($file = null)
    {
        $logPath = __DIR__ . '/../log/';

        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, true);
        }

        $logPath = $logPath . ($file ?: $this->baseUri . '.log');

        return fopen($logPath, 'w');
    }
}