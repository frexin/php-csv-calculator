<?php

namespace app\interfaces;

use app\exceptions\RowProcessorException;

interface AbstractRowProcessor
{
    /**
     * Sets default action
     * @param string $action
     * @throws RowProcessorException
     */
    public function setAction(string $action): void;

    /**
     * Add operands for evaluation
     * @param array $operands
     * @throws RowProcessorException
     */
    public function loadOperands(array $operands): void;

    /**
     * Finally evaluate expression
     * @return int
     */
    public function evaluate(): int;
}
