<?php


use app\classes\ApplicationFacade;
use app\classes\ConsoleCommandParser;
use app\classes\CsvDataProvider;
use app\classes\CsvDataStore;
use app\classes\Logger;
use app\classes\OperandCollection;
use app\classes\SimpleCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplicationFacadeTest extends TestCase
{

    /**
     * @var ApplicationFacade
     */
    protected $object;

    /**
     * @var MockObject
     */
    protected $consoleMock;

    protected function setUp()
    {
        parent::setUp();

        $this->consoleMock = $this->getMockBuilder(ConsoleCommandParser::class)
            ->setMethods(['addCommand', 'parse', 'getErrors', 'getArg'])
            ->getMock();

        $this->consoleMock->expects($this->once())->method('parse');
        $this->consoleMock->expects($this->exactly(2))->method('addCommand');
        $this->consoleMock->method('getErrors')->willReturn([]);
    }

    public function testInit()
    {
        $this->object = new ApplicationFacade(new Logger, $this->consoleMock);
        $res = $this->object->init();

        $this->assertTrue($res);

        $dataStore = new CsvDataStore;
        $dataStore->open(__DIR__ . '/../files/out.csv');

        $this->object->setDataStore($dataStore);

        return $this->object;
    }

    public function testTransformNextRow()
    {
        $callback = function ($item) {
            return $item > 0;
        };

        $rows = [
            [],
            [72, -58, 14],
            [-1, 10, 9],
            [],
            [],
            [70, -2, 68]
        ];

        $dp = $this->prepareFacade();
        $this->object->setRowProcessor(new SimpleCalculator);
        $this->object->prepareDataProvider($dp);

        foreach ($rows as $row) {
            $res = $this->object->transformNextRow($callback);
            $this->assertEquals($row, $res);
        }
    }

    public function testSetRowProcessor()
    {
        $calc = new SimpleCalculator;
        $this->prepareFacade();

        $res = $this->object->setRowProcessor($calc);
        $this->assertTrue($res);
    }

    public function testPrepareDataProvider()
    {
        $dp = $this->prepareFacade();

        $res = $this->object->prepareDataProvider($dp);
        $this->assertTrue($res);
    }

    /**
     * @return CsvDataProvider
     */
    protected function prepareFacade(): CsvDataProvider
    {
        $valueMap = [
            ['file', __DIR__ . '/../files/good.csv'],
            ['action', SimpleCalculator::ACT_ADD],
        ];

        $this->consoleMock->method('getArg')->will($this->returnValueMap($valueMap));
        $dp = new CsvDataProvider(new OperandCollection);

        $this->object = new ApplicationFacade(new Logger, $this->consoleMock);
        $this->object->init();

        return $dp;
    }
}
