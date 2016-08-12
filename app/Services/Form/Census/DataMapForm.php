<?php

namespace WA\Services\Form\Census;

use Log;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\DataMap\DataMapInterface;
use WA\Repositories\DataMapTypeRepository;
use WA\Repositories\User\UserInterface;
use WA\Services\Form\AbstractForm;

class DataMapForm extends AbstractForm
{
    /**
     * @var \WA\Parsers\ExcelCSVParser
     */
    protected $parser;

    /**
     * @var \WA\Repositories\Company\CompanyInterface
     */
    protected $company;

    /**
     * @var \WA\Repositories\User\UserInterface
     */
    protected $user;

    /**
     * @var \WA\Repositories\DataMap\DataMapInterface
     */
    protected $dataMap;

    /**
     * @param UserInterface $user
     * @param CompanyInterface  $company
     * @param DataMapInterface  $dataMap
     */
    public function __construct(UserInterface $user, CompanyInterface $company, DataMapInterface $dataMap)
    {
        $this->parser = app()->make('WA\Parsers\ExcelCSVParser');

        $this->company = $company;
        $this->user = $user;
        $this->dataMap = $dataMap;
    }

    /**
     * Get the Census Heeader from the file.
     *
     * @param string $file
     * @param string $type
     *
     * @return array
     */
    public function getCensusHeaders($file, $type)
    {
        return $this->parser->getHeaders($file, $type);
    }

    /**
     * Get employee fields that are mappable.
     *
     * @return array
     */
    public function getUserFields()
    {
        return $this->user->getMappableFields();
    }

    /**
     * Get employee fields that are mappable.
     *
     * @param $companyId
     *
     * @return array
     */
    public function getUdlFields($companyId)
    {
        return $this->company->getMappableUdlFields($companyId);
    }

    /**
     * Get the Mapping ID.
     *
     * @param $companyId
     *
     * @return mixed
     */
    public function getMappingId($companyId)
    {
        return $this->getMappingById($companyId)['id'];
    }

    /**
     * Get the census mapping of a company.
     *
     * @param $companyId
     *
     * @return Object
     */
    public function getMappingById($companyId)
    {
        return $this->dataMap->byCompany($companyId, 'census');
    }

    /**
     * Get all company information.
     *
     * @return array
     */
    public function getAllCompany()
    {
        return $this->company->byPage(false)->toArray();
    }

    /**
     * @param $companyId
     *
     * @return string company name
     */
    public function getCompanyName($companyId)
    {
        return $this->company->byId($companyId)->shortName;
    }

    /**
     * Creates a New Census Mapping.
     *
     * @param array $data
     *
     * @return bool
     */
    public function create(array $data)
    {
        if (!$this->dataMap->create($data)) {
            Log::error("Could not create new the data mapping version");

            $this->notify('error', 'Could not create the mapping at this time. Please Contact Dev');

            return false;
        }

        return true;
    }

    /**
     * @param                       $type
     * @param DataMapTypeRepository $dataMapType
     *
     * @return int of the type ID
     */
    public function getDataMapTypeId($type, DataMapTypeRepository $dataMapType = null)
    {
        $dataMapType = $dataMapType ?: app()->make('WA\Repositories\DataMapTypeRepository');

        if (!isset($dataMapType->getByName($type)['id'])) {
            return;
        }

        return $dataMapType->getByName($type)['id'];
    }

    /**
     * Deactivate previous mapping.
     *
     * @param string $versionId
     *
     * @return bool
     */
    public function deactivateMapping($versionId)
    {
        if (!$this->dataMap->deactivateMapping($versionId)) {

            Log::error("Could not deactivate previous version mapping");
            $this->notify('error', 'There was an issue update the mapping, please try later');

            return false;
        }

        return true;
    }


    /**
     * Check if there is a difference in the mapped header
     *
     * @param array $header1
     * @param array $header2
     *
     * @return bool
     */
    public function hasHeaderDiff(array $header1, array $header2)
    {
        $diff = count($header1) - count($header2);

        return (bool)abs($diff);
    }

    /**
     * Get a company by it's ID
     *
     * @param int $companyId
     *
     * @return Object
     */
    public function getCompany($companyId)
    {
        return $company = $this->company->byId($companyId);
    }
}
