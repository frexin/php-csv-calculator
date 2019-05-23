<?php

use app\classes\ApplicationFacade;
use app\classes\ConsoleCommandParser;
use app\classes\CsvDataProvider;
use app\classes\CsvDataStore;
use app\classes\Logger;
use app\classes\OperandCollection;
use app\classes\SimpleCalculator;

require_once 'vendor/autoload.php';


$callback = function ($item) {
    return $item > 0;
};

$app = new ApplicationFacade(new Logger, new ConsoleCommandParser);

if ($app->init() && $app->prepareDataProvider(new CsvDataProvider(new OperandCollection))) {

    if ($app->setRowProcessor(new SimpleCalculator)) {
        $app->setDataStore(new CsvDataStore);

        while (($row = $app->transformNextRow($callback)) !== null) {
            $app->saveTransformedRow($row);
        }
    }
}
