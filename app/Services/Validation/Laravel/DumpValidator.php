<?php

namespace WA\Services\Validation\Laravel;

use WA\Services\Validation\ValidableInterface;

/**
 * Class DumpValidator.
 */
class DumpValidator extends LaravelValidator implements ValidableInterface
{
    protected $rules = [
        'billPeriod' => 'required',
        'carrierId' => 'required',
        'numeric',
        'companyId' => 'required',
        'numeric',
    ];
}
