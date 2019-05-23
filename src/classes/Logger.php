<?php


namespace app\classes;


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{

    protected $resource;

    public function __construct(string $filename = "log.txt")
    {
        $this->resource = fopen($filename, "a");

        if (!$this->resource) {
            $this->resource = fopen("php://output", "a");
        }
    }

    public function emergency($message, array $context = [])
    {
        return $this->log(LogLevel::EMERGENCY, $message);
    }

    public function alert($message, array $context = [])
    {
        return $this->log(LogLevel::ALERT, $message);
    }

    public function critical($message, array $context = [])
    {
        return $this->log(LogLevel::CRITICAL, $message);
    }

    public function error($message, array $context = [])
    {
        return $this->log(LogLevel::ERROR, $message);
    }

    public function warning($message, array $context = [])
    {
        return $this->log(LogLevel::WARNING, $message);
    }

    public function notice($message, array $context = [])
    {
        return $this->log(LogLevel::NOTICE, $message);
    }

    public function info($message, array $context = [])
    {
        return $this->log(LogLevel::INFO, $message);
    }

    public function debug($message, array $context = [])
    {
        return $this->log(LogLevel::DEBUG, $message);
    }

    public function log($level, $message, array $context = [])
    {
        $date = date('d.m.Y H:i:s');
        $template = "[{tag}] [{date}]: {msg}\n";

        $line = $this->interpolate($template, ['date' => $date, 'tag' => $level, 'msg' => $message]);

        return fwrite($this->resource, $line);
    }

    /**
     * Interpolates context values into the message placeholders.
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate(string $message, array $context = [])
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

}
