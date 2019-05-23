<?php


namespace app\classes;


class ConsoleCommandParser
{
    protected $short_opts = [];
    protected $long_opts = [];

    protected $filled_opts = [];
    protected $errors = [];

    public function addCommand(string $long, string $short, bool $required = true): void
    {
        $long  .= $required ? ":" : "::";
        $short .= $required ? ":" : "::";

        $this->short_opts[] = $short;
        $this->long_opts[] = $long;
    }

    public function parse(): void
    {
        $this->filled_opts = getopt(implode("", $this->short_opts), $this->long_opts);

        foreach ($this->long_opts as $option) {
            if (substr($option, -2, 2) != "::") {
                $key = substr($option, 0, -1);

                if (!$this->getArg($key)) {
                    $this->errors[] = "Argument '$key' is required";
                }
            }
        }
    }

    public function getArg(string $argname): ?string
    {
        $arg = $this->filled_opts[$argname] ?? null;

        return $arg;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
