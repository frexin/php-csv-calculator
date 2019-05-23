<?php

namespace app\interfaces;

use app\exceptions\DataProviderException;

interface AbstractDataProvider
{

    /**
     * Opens data provider
     * @param string $filename Path to file
     * @throws DataProviderException
     * @return bool
     */
    public function open(string $filename): bool;

    /**
     * Runs very basic validation
     * @return bool
     */
    public function simpleValidate(): bool;

    /**
     * Returns next row from data provider
     * @return AbstractOperandCollection
     */
    public function getNextOperandsCollection(): ?AbstractOperandCollection;

    public function hasRemainingRows(): bool;
}
