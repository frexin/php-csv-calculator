<?php


namespace app\interfaces;


interface AbstractOperandCollection
{
    /**
     * Returns next operand for expression
     * @return int|null
     */
    public function getNextOperand(): ?int;

    /**
     * Returns all operands
     * @return array
     */
    public function getAllOperands(): array;

    /**
     * Initialize collection by required operands
     * @param array $operands
     * @return mixed
     */
    public function load(array $operands);

    /**
     * Removes empty and bom symbols
     * @param $operand
     * @return mixed
     */
    public static function prepareOperand($operand);
}
