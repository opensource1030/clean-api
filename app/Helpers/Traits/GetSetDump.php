<?php

namespace WA\Helpers\Traits;

use WA\DataStore\Dump;

/**
 * Class GetSetDump.
 */
trait GetSetDump
{
    /**
     * @var WA\DataStore\Dump
     */
    protected $dump;

    /**
     * @return Dump
     */
    public function getDump()
    {
        return $this->dump;
    }

    /**
     * @param Dump $dump
     */
    public function setDump(Dump $dump)
    {
        $this->dump = $dump;
    }
}
