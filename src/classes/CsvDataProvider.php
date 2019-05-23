<?php

namespace app\classes;

use app\exceptions\DataProviderException;
use app\interfaces\AbstractDataProvider;
use app\interfaces\AbstractOperandCollection;
use RuntimeException;
use SplFileObject;

class CsvDataProvider implements AbstractDataProvider
{
    /**
     * @var SplFileObject $fileObject
     */
    protected $fileObject;

    /**
     * @var bool $eof End Of File flag
     */
    protected $eof = false;

    /**
     * @var AbstractOperandCollection
     */
    protected $operandCollection;

    /**
     * CsvDataProvider constructor.
     * @param AbstractOperandCollection $operandCollection
     */
    public function __construct(AbstractOperandCollection $operandCollection)
    {
        $this->operandCollection = $operandCollection;
    }

    /**
     * Opens CSV file for reading
     * @param string $filename
     * @return bool
     * @throws DataProviderException
     */
    public function open(string $filename): bool
    {
        try {
            $this->fileObject = new SplFileObject($filename);
            $this->fileObject->setFlags(SplFileObject::READ_CSV);
            $this->fileObject->setCsvControl(";");
        } catch (RuntimeException $e) {
            throw new DataProviderException("Unable to open CSV file");
        }

        if (!$this->fileObject->isReadable()) {
            throw new DataProviderException("CSV file is not readable");
        }

        return true;
    }

    /**
     * Checks if first line is valid
     * @return bool
     */
    public function simpleValidate(): bool
    {
        $result = false;

        $row = $this->fileObject->current();

        if ($row) {
            $result = $this->validateRow($row);
        }

        return $result;
    }

    public function getNextOperandsCollection(): ?AbstractOperandCollection
    {
        $result = null;

        if ($row = $this->getNextRow()) {
            $this->operandCollection->load($row);

            return $this->operandCollection;
        }

        return $result;
    }

    public function hasRemainingRows(): bool
    {
        return !$this->eof;
    }


    protected function getNextRow(): ?array
    {
        $result = null;

        if ($this->fileObject->valid()) {
            $row = $this->fileObject->current();

            if ($this->validateRow($row)) {
                $result = $this->prepareRow($row);
            }

            $this->fileObject->next();
        }
        else {
            $this->eof = true;
        }

        return $result;
    }

    protected function validateRow($row)
    {
        $arr = array_filter($row, function($val) {
            $val = $this->operandCollection::prepareOperand($val);

            return is_numeric($val);
        });

        $result = count($arr) >= 2;

        return $result;
    }

    protected function prepareRow(array $row)
    {
        array_walk($row, function (&$item) {
            $item = $this->operandCollection::prepareOperand($item);
        });

        return $row;
    }
}
