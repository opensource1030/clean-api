<?php

namespace WA\Services\Form\Employee;

use Log;
use WA\DataLoader\DataParserInterface;
use WA\Repositories\Company\CompanyInterface;
use WA\Services\Form\AbstractForm;

/**
 * Class BulkEmployeeForm.
 */
class BulkEmployeeForm extends AbstractForm
{
    /**
     * @var \WA\DataLoader\DataParserInterface
     */
    protected $parser;

    /**
     * @var \WA\Repositories\Company\CompanyInterface
     */
    protected $company;

    /**
     * Bag of errors reported back to the user.
     *
     * @var array
     */
    protected $error = [];

    /**
     * @var \WA\Repositories\Census\CensusInterface
     */
    protected $census;
    /**
     * @var BulkEmployeeFormValidator
     */
    protected $validator;

    /**
     * @param BulkEmployeeFormValidator $validator
     * @param CompanyInterface          $company
     * @param DataParserInterface       $parser
     */
    public function __construct(
        BulkEmployeeFormValidator $validator,
        CompanyInterface $company,
        DataParserInterface $parser
    ) {
        $this->validator = $validator;
        $this->company = $company;
        $this->parser = $parser;
    }

    /**
     * @param array $input
     *
     * @return bool
     */
    public function save(array $input)
    {
        $company = $this->company->byId($input['companyId']);
        $headers = $input['headers'];
        $mapping = $headers['saved']['mapping'];

        $employees = $this->loadFile($input['file'], $headers['saved']['required']);

        $newKeys = array_map(
            function ($i) {
                return strtolower(str_replace(' ', '_', $i));
            },
            array_keys($mapping)
        );

        $mappedHead = array_combine($newKeys, array_values($mapping));

        $udlValues = [];
        $staticData = [];

        $this->company->createUDLs($company->id, $headers['saved']['udls']);
        $censusId = $this->company->createCensus($input['companyId'], 'loaded');

        foreach ($employees as $employee) {
            try {
                foreach ($mappedHead as $v => $o) {
                    if (starts_with($o, 'udl')) {
                        $udlValues[$o] = $employee[$v];
                    } else {
                        $staticData[$o] = $employee[$v];
                    }
                }

                $staticData['udlValues'] = $udlValues;
                $staticData['companyId'] = $company->id;
                $staticData['censusId'] = $censusId;
                $staticData['isActive'] = 1;

                $this->company->addEmployee($company->id, $staticData);
            } catch (\Exception $e) {
                $this->notify('error', 'Bringing in the user failed.... this has been noted, try again later.');

                Log::error('['.get_class().'] | [ '.$e->getLine().' ] '.$e->getMessage());

                return false;
            }
        }

        $this->company->updateCensus($input['companyId'], $censusId, 'complete');
        $this->company->syncEmployeeSupervisor($censusId);

        return true;
    }

    /**
     * Load the needed file, based on this parser.
     *
     * @param $filePath
     * @param array $columns
     * @param int   $take
     *
     * @return Object of loaded file
     */
    protected function loadFile($filePath, array $columns, $take = 0)
    {
        $parser = $this->parser->getDataParserInstance();
        $read = $parser->load($filePath);

        if (!empty($columns)) {
            $columns = array_map(
                function ($c) {
                    return str_replace(' ', '_', strtolower($c));
                },
                $columns
            );

            return $read->get($columns);
        }

        return (!(bool) $take) ? $read->all() : $read->take($take);
    }

    /**
     * Get the headers from the uploaded file, if it matches, also return the file header.
     *
     * @param array $input
     *
     * @return array headers | false
     */
    public function getHeaders(array $input)
    {
        if (!$this->valid($input)) {
            return false;
        }

        $censusHeader = $this->company->getDataHeader($input['companyId'], 'census');
        $company = $this->company->byId($input['companyId']);

        if (empty($censusHeader)) {
            $this->notify('error', 'An Header does not exist for your selected company -- Please add the header');

            return false;
        }

        $filePath = $input['file'];
        $censusHeader = json_decode($censusHeader, true);
        $fileHeader = $this->parser->getHeaders($filePath);
        $requiredHeader = implode(',', $censusHeader['required']);

        if (!$this->parser->matchHeaders($filePath, $censusHeader)) {
            $this->notify('error', 'Please verify that the loaded file has the minimum required fields: $requiredHeader');

            return false;
        }

        $this->notify('success', 'Everything seems OK for $company->name, review the data continue');

        $headers = ['saved' => $censusHeader, 'file' => $fileHeader];

        return $headers;
    }

    /**
     * Verify that data passes.
     *
     * @param array $data
     *
     * @return bool
     */
    public function valid(array $data)
    {
        return $this->validator->with($data)->passes();
    }

    /**
     * Get error is there is any any the validation.
     *
     * @return array
     */
    public function errors()
    {
        return $this->validator->errors();
    }
}
