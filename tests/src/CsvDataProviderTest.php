<?php

use app\classes\CsvDataProvider;
use app\classes\OperandCollection;
use app\interfaces\AbstractOperandCollection;
use PHPUnit\Framework\TestCase;

class CsvDataProviderTest extends TestCase
{

    /**
     * @var CsvDataProvider
     */
    protected $object;
    private $good_csv;
    private $bad_csv;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new CsvDataProvider(new OperandCollection);

        $this->good_csv = __DIR__ . '/../files/good.csv';
        $this->bad_csv = __DIR__ . '/../files/bad.csv';

    }

    public function testGetNextOperandsCollection()
    {
        $this->object->open($this->good_csv);
        $col = $this->object->getNextOperandsCollection();

        $this->assertInstanceOf(AbstractOperandCollection::class, $col);

    }

    public function testSimpleValidate()
    {
        $this->object->open($this->good_csv);
        $this->assertTrue($this->object->simpleValidate());

        $this->object->open($this->bad_csv);
        $this->assertFalse($this->object->simpleValidate());

        $this->object->open(__DIR__ . '/../files/bad2.csv');
        $this->assertFalse($this->object->simpleValidate());

    }

    public function testOpen()
    {
        $res = $this->object->open($this->good_csv);
        $this->assertTrue($res);
    }

    public function testHasRemainingRows()
    {
        $this->object->open($this->good_csv);
        $this->assertTrue($this->object->hasRemainingRows());

        for ($i = 0; $i <= 10; $i++) {
            $this->object->getNextOperandsCollection();
        }

        $this->assertFalse($this->object->hasRemainingRows());
    }
}
