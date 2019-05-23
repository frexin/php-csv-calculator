<?php


namespace app\classes;


use app\interfaces\AbstractOperandCollection;

class OperandCollection implements AbstractOperandCollection
{

    protected $operands = [];

    public function load(array $numbers)
    {
        $this->operands = $numbers;
    }

    public function getNextOperand(): ?int
    {
        $result = null;

        if ($this->operands) {
            $result = intval(array_shift($this->operands));
        }

        return $result;
    }

    public function getAllOperands(): array
    {
        $result = [];

        while (($op = $this->getNextOperand()) !== null) {
            array_push($result, $op);
        }

        return $result;
    }


    public static function prepareOperand($operand)
    {
        return trim($operand, "\0\t\xEF\xBB\xBF "); // removes bom and empty symbols
    }
}
