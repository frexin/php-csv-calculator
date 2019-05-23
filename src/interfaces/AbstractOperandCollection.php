<?php


namespace app\interfaces;


interface AbstractOperandCollection
{
    public function getNextOperand(): ?int;

    public function getAllOperands(): array;

    public function load(array $operands);

    public static function prepareOperand($operand);
}
