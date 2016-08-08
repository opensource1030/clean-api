<?php

namespace WA\Services\ApiHandler;

interface ParserInterface
{
    /**
     * Parse the query parameters with the given options.
     * Either for a single dataset or multiple.
     *
     * @param mixed $options
     * @param bool  $multiple
     */
    public function parse($options, $multiple = false);

    /**
     * Get the currently passed in config option.
     *
     * @param $config
     *
     * @return mixed
     */
    public function getConfig($config);

    /**
     * Get the builder instance.
     *
     * @return mixed
     */
    public function getBuilder();

    /**
     * Get the returned multiple setting.
     *
     * @return mixed
     */
    public function getMultiple();
}
