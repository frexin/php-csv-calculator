<?php


namespace app\classes;


use app\exceptions\RowProcessorException;
use app\interfaces\AbstractRowProcessor;
use DivisionByZeroError;

class SimpleCalculator implements AbstractRowProcessor
{

    const ACT_ADD  = "plus";
    const ACT_SUB  = "minus";
    const ACT_MULT = "multiply";
    const ACT_DIV  = "division";

    private $action = null;
    private $operands = [];

    public function getEvaluators(): array
    {
        return [
            self::ACT_ADD => function ($a, $b) {
                return $a + $b;
            },
            self::ACT_SUB => function ($a, $b) {
                return $a - $b;
            },
            self::ACT_MULT => function ($a, $b) {
                return $a * $b;
            },
            self::ACT_DIV => function ($a, $b) {
                try {
                    return intdiv($a, $b);
                } catch (DivisionByZeroError $e) {
                    throw new RowProcessorException("Division by zero!");
                }
            }
        ];
    }

    public function setAction(string $action): void
    {
        if (!in_array($action, array_keys($this->getEvaluators()))) {
            throw new RowProcessorException("Action $action does not exist");
        }

        $this->action = $action;
    }

    public function loadOperands(array $operands): void
    {
        $this->operands = [];

        foreach ($operands as $operand) {
            if (!is_int($operand)) {
                throw new RowProcessorException("Operand should be an integer");
            }

            $this->operands[] = $operand;
        }

        if (count($this->operands) < 2) {
            throw new RowProcessorException("You have to specify at least two operands");
        }
    }

    public function evaluate(): int
    {
        if (!$this->action) {
            throw new RowProcessorException("You have to specify action");
        }

        $first_operand = array_shift($this->operands);
        $result = array_reduce($this->operands, function ($carry, $item) {
            $evaluator = $this->getEvaluators()[$this->action];
            $result = $evaluator($carry, $item);

            return $result;
        }, $first_operand);

        return $result;
    }
}
