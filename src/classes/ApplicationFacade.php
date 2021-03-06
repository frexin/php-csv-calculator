<?php


namespace app\classes;


use app\exceptions\DataStoreException;
use app\exceptions\RowProcessorException;
use app\exceptions\DataProviderException;
use app\interfaces\AbstractDataProvider;
use app\interfaces\AbstractDataStore;
use app\interfaces\AbstractRowProcessor;
use Closure;
use Psr\Log\LoggerInterface;

class ApplicationFacade
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AbstractDataProvider
     */
    protected $dataProvider;

    /**
     * @var AbstractDataStore
     */
    protected $dataStore;

    /**
     * @var ConsoleCommandParser
     */
    protected $consoleParser;

    /**
     * @var AbstractRowProcessor
     */
    protected $rowProcessor;

    /**
     * ApplicationFacade constructor.
     * @param LoggerInterface $logger
     * @param ConsoleCommandParser $consoleParser
     */
    public function __construct(LoggerInterface $logger, ConsoleCommandParser $consoleParser)
    {
        $this->logger = $logger;
        $this->consoleParser = $consoleParser;
    }

    /**
     * Sets required console arguments
     * @return bool
     */
    public function init(): bool
    {
        $result = true;

        $this->consoleParser->addCommand("action", "a");
        $this->consoleParser->addCommand("file", "f");

        $this->consoleParser->parse();

        if ($errors = $this->consoleParser->getErrors()) {
            foreach ($errors as $error) {
                $this->showError($error);
            }

            $result = false;
        }

        return $result;
    }

    /**
     * Opens data providers and runs validation
     * @param AbstractDataProvider $provider
     * @return bool
     */
    public function prepareDataProvider(AbstractDataProvider $provider): bool
    {
        $result = true;

        try {
            $this->dataProvider = $provider;
            $this->dataProvider->open($this->consoleParser->getArg('file'));
        } catch (DataProviderException $e) {
            $this->showError($e->getMessage());
            return false;
        }

        if (!$this->dataProvider->simpleValidate()) {
            $this->showError("Data file is invalid");
            $result = false;
        }

        return $result;
    }

    /**
     * Sets default processor for each entry in file
     * @param AbstractRowProcessor $rowProcessor
     * @return bool
     */
    public function setRowProcessor(AbstractRowProcessor $rowProcessor): bool
    {
        $result = true;
        $this->rowProcessor = $rowProcessor;

        try {
            $this->rowProcessor->setAction($this->consoleParser->getArg('action'));
        } catch (RowProcessorException $e) {
            $this->showError($e->getMessage());
            $result = false;
        }

        return $result;
    }

    /**
     * Sets data store for saving transformed entries
     * @param AbstractDataStore $dataStore
     */
    public function setDataStore(AbstractDataStore $dataStore): void
    {
        $this->dataStore = $dataStore;

        try {
            $this->dataStore->open();
        } catch (DataStoreException $e) {
            $this->showError($e->getMessage());
        }
    }

    /**
     * Saves the new transformed row in the data store
     * @param array $row
     * @return bool
     */
    public function saveTransformedRow(array $row): bool
    {
        return $this->dataStore->addRow($row);
    }

    /**
     * Runs row transformation process
     * @param Closure $resultRestriction
     * @return array|null
     */
    public function transformNextRow(Closure $resultRestriction): ?array
    {
        $row = [];

        if ($this->dataProvider->hasRemainingRows() && $col = $this->dataProvider->getNextOperandsCollection()) {
            try {
                $operands = $col->getAllOperands();
                $this->rowProcessor->loadOperands($operands);
                $result = $this->rowProcessor->evaluate();

                if (!$resultRestriction($result)) {
                    $this->logger->notice(sprintf("Numbers %s and %s are wrong", $operands[0], $operands[1]));
                } else {
                    $row = array_merge($operands, [$result]);
                }
            } catch (RowProcessorException $e) {
                $this->showError($e->getMessage());
            }
        } else {
            $row = null;
        }

        return $row;
    }

    /**
     * Sends error message to the log and console
     * @param string $error
     */
    public function showError(string $error): void
    {
        $this->logger->error($error);
        print($error . PHP_EOL);
    }
}
