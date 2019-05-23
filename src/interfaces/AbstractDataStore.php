<?php


namespace app\interfaces;


use app\exceptions\DataStoreException;

interface AbstractDataStore
{
    /**
     * Opens new file for saving transformed entries
     * @param string $filename
     * @throws DataStoreException
     * @return bool
     */
    public function open(string $filename = "result.csv"): bool;

    /**
     * Adds new row to the data store
     * @param array $row Transformed row
     * @return bool
     */
    public function addRow(array $row): bool;
}
