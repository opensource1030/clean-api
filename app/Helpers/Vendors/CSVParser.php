<?php


namespace WA\Helpers\Vendors;

use WA\Helpers\Vendors\CSV;

/**
 * Class CSVParser.
 */
class CSVParser extends CSV
{
    /**
     * Gets the internally parsed rows as an array.
     *
     * @return array
     */
    public function getRows()
    {
        if (!isset($this->rows)) {
            $this->parse();
        }
        
        return $this->rows;
    }

    /**
     * Gets the internally parsed rows as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->toJSON(), true);
    }
}
