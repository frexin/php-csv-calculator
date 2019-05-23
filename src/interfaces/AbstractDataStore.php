<?php


namespace app\interfaces;


use app\exceptions\DataStoreException;

interface AbstractDataStore
{
    /**
     * @param string $filename
     * @throws DataStoreException
     * @return bool
     */
    public function open(string $filename = "result.csv"): bool;

    public function addRow(array $row): bool;
}
