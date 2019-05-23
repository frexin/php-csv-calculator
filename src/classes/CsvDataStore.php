<?php


namespace app\classes;


use app\exceptions\DataStoreException;
use app\interfaces\AbstractDataStore;

class CsvDataStore implements AbstractDataStore
{
    protected $resource;

    public function open(string $filename = "result.csv"): bool
    {
        $this->resource = fopen($filename, 'w');

        if (!$this->resource) {
            throw new DataStoreException("Unable to create data file");
        }

        return $this->resource !== false;
    }

    public function addRow(array $row): bool
    {
        $res = false;

        if ($this->resource && $row) {
            $res = fputcsv($this->resource, $row, ";");
        }

        return $res;
    }
}
