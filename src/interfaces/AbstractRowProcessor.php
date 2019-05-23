<?php

namespace app\interfaces;

use app\exceptions\RowProcessorException;

interface AbstractRowProcessor
{
    /**
     * @param string $action
     * @throws RowProcessorException
     */
    public function setAction(string $action): void;

    /**
     * @param array $operands
     * @throws RowProcessorException
     */
    public function loadOperands(array $operands): void;

    public function evaluate(): int;
}
