<?php


use app\classes\SimpleCalculator;
use app\exceptions\RowProcessorException;
use PHPUnit\Framework\TestCase;

class SimpleCalculatorTest extends TestCase
{

    /**
     * @var SimpleCalculator
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new SimpleCalculator();
    }

    public function incorrectOperands() {
        return [
            ["err", 1],
            [1, "err"],
            [34]
        ];
    }

    public function correctOperands() {
        return [
            [1, 2, 3, SimpleCalculator::ACT_ADD],
            [10, 2, 8, SimpleCalculator::ACT_SUB],
            [10, 5, 50, SimpleCalculator::ACT_MULT],
            [8, 2, 4, SimpleCalculator::ACT_DIV]
        ];
    }


    /**
     * @dataProvider incorrectOperands
     */
    public function testLoadIncorrectOperands()
    {
        $this->expectException(RowProcessorException::class);
        $this->object->loadOperands(func_get_args());

    }

    public function testSetAction()
    {
        $this->expectException(RowProcessorException::class);
        $this->object->setAction("incorrect");

    }

    public function testGetEvaluators()
    {
        $evaluators = $this->object->getEvaluators();

        $this->assertIsArray($evaluators);
        $this->assertCount(4, $evaluators);
    }

    /**
     * @dataProvider correctOperands
     */
    public function testEvaluate($a, $b, $result, $action)
    {
        $this->object->setAction($action);
        $this->object->loadOperands([$a, $b]);

        $this->assertEquals($result, $this->object->evaluate());
    }

    public function testDivByZero() {
        $this->expectException(RowProcessorException::class);
        $this->object->setAction(SimpleCalculator::ACT_DIV);

        $this->object->loadOperands([5, 0]);
        $this->object->evaluate();
    }
}
