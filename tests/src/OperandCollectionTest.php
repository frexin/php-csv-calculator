<?php


use app\classes\OperandCollection;
use app\classes\SimpleCalculator;
use PHPUnit\Framework\TestCase;

class OperandCollectionTest extends TestCase
{

    /**
     * @var OperandCollection
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new OperandCollection();
    }

    public function badStrings() {
        return [
            ["  test  ", "test"],
            ["\xEF\xBB\xBFtest0 ", "test0"],
            ["\t23", "23"]
        ];
    }

    public function testGetAllOperands()
    {
        $this->object->load(["1", "2", "-10"]);
        $ops = $this->object->getAllOperands();

        $this->assertCount(3, $ops);

        $this->object->load([]);
        $this->assertEmpty($this->object->getAllOperands());

    }

    public function testGetNextOperand()
    {
        $this->object->load(["125", "-10"]);

        $op = $this->object->getNextOperand();
        $this->assertEquals(125, $op);

        $op = $this->object->getNextOperand();
        $this->assertEquals(-10, $op);

        $op = $this->object->getNextOperand();
        $this->assertNull($op);
    }

    /**
     * @dataProvider badStrings
     */
    public function testPrepareOperand($haystack, $expect)
    {
        $res = OperandCollection::prepareOperand($haystack);
        $this->assertEquals($expect, $res);
    }
}
