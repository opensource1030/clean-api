<?php

namespace WA\Services\Form\Company;

use WA\Services\Validation\AbstractLaravelValidator;


/**
 * Class CompanyFormValidator.
 */
class CompanyFormValidator extends AbstractLaravelValidator
{
    /**
     * Validation rules.
     *
     * @var Array
     */

    protected $rules = [
        'name' => 'required',
        'shortName' => 'required'
    ];
 }
