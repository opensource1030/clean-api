<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Queue;
use WA\DataStore\Company\Company;
use WA\DataStore\Company\CompanyTransformer;
use WA\DataStore\Company\CompanyUserImportJob;
use WA\DataStore\Company\CompanyUserImportJobTransformer;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Company\CompanyUserImportJobInterface;
use WA\DataStore\Udl\Udl;
use WA\DataStore\Udl\UdlTransformer;
use WA\Repositories\Udl\UdlInterface;

use WA\Helpers\Vendors\CSVParser;
use WA\Jobs\ImportBulkUsersJob;
use WA\DataStore\User\User;

/**
 * Class CompaniesController.
 */
class CompaniesController extends FilteredApiController {
	/**
	 * @var CompanyInterface
	 */
	protected $company;

	/**
	 * @var CompanyUserImportJobInterface
	 */
	protected $companyUserImportJob;

	/**
	 * @var CarrierInterface
	 */
	protected $carrier;

	/**
	 * @var UdlInterface
	 */
	protected $udl;

	/**
	 * CompaniesController constructor.
	 *
	 * @param CompanyInterface $company
	 * @param CarrierInterface $carrier
	 * @param UdlInterface $udl
	 * @param Request $request
	 */
	public function __construct(
		CompanyInterface $company,
		CompanyUserImportJobInterface $companyUserImportJob,
		UdlInterface $udl,
		Request $request
	) {
		parent::__construct($company, $request);
		$this->company = $company;
		$this->companyUserImportJob = $companyUserImportJob;
		$this->udl = $udl;
	}

	/**
	 * Update a company.
	 *
	 * @param $id
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function store($id, Request $request) {
		$success = true;

		/*
			         * Checks if Json has data, data-type & data-attributes.
		*/
		if (!$this->isJsonCorrect($request, 'companies')) {
			$error['errors']['json'] = Lang::get('messages.InvalidJson');

			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}

		DB::beginTransaction();

		try {
			$data = $request->all()['data'];
			$data['attributes']['id'] = $id;
			$company = $this->company->update($data['attributes']);

			if ($company == 'notExist') {
				DB::rollBack();
				$error['errors']['company'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);
				//$error['errors']['Message'] = $e->getMessage();
				return response()->json($error)->setStatusCode($this->status_codes['notexists']);
			}

			if ($company == 'notSaved') {
				DB::rollBack();
				$error['errors']['company'] = Lang::get('messages.NotSavedClass', ['class' => 'Company']);
				//$error['errors']['Message'] = $e->getMessage();
				return response()->json($error)->setStatusCode($this->status_codes['conflict']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			$error['errors']['company'] = Lang::get('messages.NotOptionIncludeClass',
				['class' => 'Company', 'option' => 'updated', 'include' => '']);
			//$error['errors']['Message'] = $e->getMessage();
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}

		/*
			         * Check if Json has relationships to continue or if not and commit + return.
		*/

		if (isset($data['relationships']) && $success) {
			if (isset($data['relationships']['addresses'])) {
				if (isset($data['relationships']['addresses']['data'])) {

					$addressInterface = app()->make('WA\Repositories\Address\AddressInterface');
					$dataAddresses = $data['relationships']['addresses']['data'];

					$addressIdArray = [];

					foreach ($dataAddresses as $item) {
						try {
							if ($item['id'] > 0) {
								array_push($addressIdArray, $item);
							} else {
								$newAddress = $addressInterface->create($item['attributes']);
								//\Log::debug("NEW ADDRESS: " . print_r($newAddress, true));
								$aux['id'] = $newAddress->id;
								$aux['type'] = 'addresses';
								array_push($addressIdArray, $aux);
							}

							$dataAddress = $this->parseJsonToArray($addressIdArray, 'addresses');
							$company->addresses()->sync($dataAddress);
						} catch (\Exception $e) {
							DB::rollBack();
							$error['errors']['addresses'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Company', 'option' => 'created', 'include' => 'Address']);
							$error['errors']['Message'] = $e->getMessage();
							return response()->json($error)->setStatusCode($this->status_codes['conflict']);
						}
					}
				}
			}

			if (isset($data['relationships']['udls'])) {
				if (isset($data['relationships']['udls']['data'])) {

					try {
						$udl = Udl::where('companyId', $company->id)->get();

						$udlInterface = app()->make('WA\Repositories\Udl\UdlInterface');
						$this->deleteNotRequested($data['relationships']['udls']['data'], $udl, $udlInterface, 'udls');

						$helper = app()->make('WA\Http\Controllers\UdlsHelperController');
						$success = $helper->create($data['relationships']['udls'], $company->id);

						if (!$success) {
							$error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Udl', 'option' => 'updated', 'include' => '']);
						}

					} catch (\Exception $e) {
						$success = false;
						$error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Udl', 'option' => 'updated', 'include' => '']);
						//$error['errors']['Message'] = $e->getMessage();
					}
				}
			}

			if (isset($data['relationships']['globalsettingvalues'])) {
                if (isset($data['relationships']['globalsettingvalues']['data'])) {
                    try {
                        $dataGlobalSettingValues = $this->parseJsonToArray($data['relationships']['globalsettingvalues']['data'], 'globalsettingvalues');
                        $company->globalsettingvalues()->sync($dataGlobalSettingValues);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['globalsettingvalues'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Company', 'option' => 'created', 'include' => 'Globalsettingvalues']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
		}

		if ($success) {
			DB::commit();
			return $this->response()->item($company, new CompanyTransformer(),
				['key' => 'companies'])->setStatusCode($this->status_codes['created']);
		} else {
			DB::rollBack();
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}
	}
	/**
	 * Create a new company.
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function create(Request $request) {
		$success = true;

		if (!$this->isJsonCorrect($request, 'companies')) {
			$error['errors']['json'] = Lang::get('messages.InvalidJson');
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		} else {
			$data = $request->all()['data'];
		}

		DB::beginTransaction();

		try {
			$company = $this->company->create($data['attributes']);
			if(!$company) {
				DB::rollBack();
				$error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass',
					['class' => 'Company', 'option' => 'created', 'include' => '']);
				$error['errors']['Message'] = $e->getMessage();
				return response()->json($error)->setStatusCode($this->status_codes['conflict']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			$error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass',
				['class' => 'Company', 'option' => 'created', 'include' => '']);
			$error['errors']['Message'] = $e->getMessage();
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}

		/*
			         * Check if Json has relationships to continue or if not and commit + return.
		*/
		if (isset($data['relationships']) && $success) {
			if (isset($data['relationships']['addresses'])) {
				if (isset($data['relationships']['addresses']['data'])) {

					$addressInterface = app()->make('WA\Repositories\Address\AddressInterface');
					$dataAddresses = $data['relationships']['addresses']['data'];

					$addressIdArray = [];

					foreach ($dataAddresses as $item) {
						try {
							//\Log::debug("item: " . print_r($item, true));
							if ($item['id'] > 0) {
								array_push($addressIdArray, $item);
							} else {
								$newAddress = $addressInterface->create($item['attributes']);
								//\Log::debug("NEW ADDRESS: " . print_r($newAddress, true));
								$aux['id'] = $newAddress->id;
								$aux['type'] = 'addresses';
								array_push($addressIdArray, $aux);
							}

							$dataAddress = $this->parseJsonToArray($addressIdArray, 'addresses');
							$company->addresses()->sync($dataAddress);
						} catch (\Exception $e) {
							DB::rollBack();
							$error['errors']['addresses'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Company', 'option' => 'created', 'include' => 'Address']);
							$error['errors']['Message'] = $e->getMessage();
							return response()->json($error)->setStatusCode($this->status_codes['conflict']);
						}
					}
				}
			}

			if (isset($data['relationships']['udls'])) {
				if (isset($data['relationships']['udls']['data'])) {

					try {

						$helper = app()->make('WA\Http\Controllers\UdlsHelperController');
						$success = $helper->create($data['relationships']['udls'], $company->id);

						if (!$success) {
							$error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Udl', 'option' => 'created', 'include' => '']);
						}

					} catch (\Exception $e) {
						$success = false;
						$error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Udl', 'option' => 'created', 'include' => '']);
						//$error['errors']['Message'] = $e->getMessage();
					}
				}
			}

			if (isset($data['relationships']['globalsettingvalues'])) {
                if (isset($data['relationships']['globalsettingvalues']['data'])) {
                    try {
                        $dataGlobalSettingValues = $this->parseJsonToArray($data['relationships']['globalsettingvalues']['data'], 'globalsettingvalues');
                        $company->globalsettingvalues()->sync($dataGlobalSettingValues);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['globalsettingvalues'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Company', 'option' => 'created', 'include' => 'Globalsettingvalues']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
		}

		if ($success) {
			DB::commit();
			return $this->response()->item($company, new CompanyTransformer(),
				['key' => 'companies'])->setStatusCode($this->status_codes['created']);
		} else {
			DB::rollBack();
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}
	}

	/**
	 * Delete a company.
	 *
	 * @param $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteCompany($id) {
		$company = Company::find($id);
		if ($company != null) {
			$this->company->deleteById($id);
		} else {
			$error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);
			return response()->json($error)->setStatusCode($this->status_codes['notexists']);
		}

		$company = Company::find($id);
		if ($company == null) {
			return array("success" => true);
		} else {
			$error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Company']);
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}
	}

	/*
	 * BULK EMPLOYEES JOBS
	 */

	/**
	 * Update the information related to the job.
	 *
	 * @param $companyId
	 * @param $jobId
	 * @param Request $request
	 * @return mixed
	 */
	public function storeJob($companyId, $jobId, Request $request) {

		if (!$this->isJsonCorrect($request, 'jobs')) {
			$error['errors']['json'] = Lang::get('messages.InvalidJson');
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}

		// check if company is exist
		$company = Company::find($companyId);
		if ($company == null) {
			$error['errors']['company'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);
			return response()->json($error)->setStatusCode($this->status_codes['notexists']);
		}

		// get job details
		$criteria = $this->getRequestCriteria();
        $this->companyUserImportJob->setCriteria($criteria);
		$job = $this->companyUserImportJob->byId($jobId);

		if ($job == null) {
			$error['errors']['job'] = Lang::get('messages.NotExistClass', ['class' => 'Job']);
			return response()->json($error)->setStatusCode($this->status_codes['notexists']);
		}

		// request data
		// check mapping is array and not empty
		$data = $request->all()['data'];
		if (!isset($data['attributes'])
			|| !isset($data['attributes']['mappings'])
			|| !is_array($data['attributes']['mappings'])
			|| empty($data['attributes']['mappings'])) {
			$error['errors']['job'] = Lang::get('messages.InvalidJson');
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}

		// Update the current status of the job:
		$data['attributes']['id'] = $jobId;
		$companyUserImportJob = $this->companyUserImportJob->update($data["attributes"]); //TODO: CARLOS
		$jobForUsersImportation = new ImportBulkUsersJob($jobId, $companyUserImportJob);

		// \Log::debug("Send to dispatch job");
		// $enqueuedJobID = Queue::push($jobForUsersImportation);
		$jobForUsersImportation->onQueue('default');
		$jobId = dispatch($jobForUsersImportation);
		// \Log::debug("Sent to dispatch job");

		// tests:
		$response = $this->response->item($job, $job->getTransformer(), ['key' => 'companyuserimportjob']);
		$response = $this->applyMeta($response);
		// \Log::debug($job);

		return $response;
	}

	/**
	 * Create a job and return the details.
	 *
	 * @param $companyId
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createJob($companyId, Request $request) {

		$company = Company::find($companyId);
		if ($company == null) {
			$error['errors']['company'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);
			return response()->json($error)->setStatusCode($this->status_codes['notexists']);
		}

		$subFolder = str_slug($company->name);
		$storagePath = 'clients' . DIRECTORY_SEPARATOR . $subFolder;
		$file = $request->file('csv');
		$fileName = null;
		$fileExtension = null;

		// check if files is exist
		if (is_null($file)) {
			$error['errors']['csv'] = Lang::get('messages.NotExistFile');
			return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
		}

		// check if file extension is csv
		$originalFileName = $file->getClientOriginalName();
		$fileName = pathinfo($originalFileName, PATHINFO_FILENAME) . ($request->has('test') ? '' : time());
		$fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
		if ($fileExtension !== 'csv') {
			$error['errors']['file'] = Lang::get('messages.NotRightFile', ['class' => 'csv']);
			return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
		}

		// move file to storage
		$uploadedFileName = str_slug($fileName) . '.' . $fileExtension;
		if (!$request->has('test')) {

			if (!($file = $file->move(storage_path($storagePath), $uploadedFileName))) {
				$error['errors']['file'] = Lang::get('messages.NotExistPath');
				return response()->json($error)->setStatusCode($this->status_codes['forbidden']);
			}
		}

		$filePath = $file->getRealPath();
		$csvParser = new CSVParser($filePath);
		$rows = $csvParser->getRows(true);

		// check file is empty
		if (count($rows) == 0) {
			$error['errors']['file'] = Lang::get('messages.EmptyFile');
			return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
		}

		$numberOfUsers = $this->creatableOrUpdatable($rows);

        $data = [
			"jobType" => "employeeimportjob",
		    "companyId" => $companyId,
            "filepath" => $filePath,
            "filename" => $uploadedFileName,
            "totalUsers" => 0,
            "createdUsers" => 0,
            "creatableUsers" => $numberOfUsers['creatableUsers'],
            "updatedUsers" => 0,
            "updatableUsers" => $numberOfUsers['updatableUsers'],
            "failedUsers" => 0,
            "fields" => $rows[0],
            "sampleUser" => $this->getSampleUser($rows),
            "mappings" => $this->getMappingsFromOtherJobs($companyId),
            "status" => 0,
            "created_by_id" => 1, // Auth::user()->id,
            "updated_by_id" => 1, // Auth::user()->id,
        ];

        $dbFields = $this->retrieveDBFields($companyId);
        
		// Create the job:
		$companyUserImportJob = $this->companyUserImportJob->create($data);// --> ELOQUENT.
		$companyUserImportJob->dbfields = $dbFields;
		
		// \Log::debug(json_encode($companyUserImportJob, JSON_PRETTY_PRINT));

		return $this->response()->item(
			$companyUserImportJob,
			$companyUserImportJob->getTransformer(),
			['key' => 'companyuserimportjobs']
		)->setStatusCode($this->status_codes['created']);
		
	}

	/**
	 * Retrieve the information related to the job.
	 *
	 * @param $companyId
	 * @param $jobId
	 * @return mixed
	 */
	public function showJobs($companyId, Request $request) {

		$filter = $this->companyUserImportJob->addFilterToTheRequest($companyId);
        $this->setExtraFilters($filter);
        $criteria = $this->getRequestCriteria();
        $this->companyUserImportJob->setCriteria($criteria);

        $resource = $this->companyUserImportJob->byPage();

        $transformer = $this->companyUserImportJob->getTransformer();

        if (!$this->includesAreCorrect($request, $transformer)) {
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->paginator($resource, $transformer, ['key' => 'companyuserimportjobs']);
        $response = $this->applyMeta($response);

        return $response;
    }

	/**
	 * Retrieve the information related to the job.
	 *
	 * @param $companyId
	 * @param $jobId
	 * @return mixed
	 */
	public function showJob($companyId, $jobId, Request $request) {
        $criteria = $this->getRequestCriteria();
        $this->companyUserImportJob->setCriteria($criteria);
        $resource = $this->companyUserImportJob->byId($jobId);

        if ($resource === null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => $this->modelName]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $transformer = $this->companyUserImportJob->getTransformer();

        if (!$this->includesAreCorrect($request, $transformer)) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->item($resource, $transformer, ['key' => 'companyuserimportjobs']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
	 * Delete a job.
	 *
	 * @param $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteJob($companyId, $jobId) {
		$job = $this->companyUserImportJob->byId($jobId);

		if ($job == null) {
			$error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Job']);
			return response()->json($error)->setStatusCode($this->status_codes['notexists']);
		}

		if ($job->status == 1) {
			$error['errors']['delete'] = 'A queued Job can\'t be deleted';
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}

		$this->companyUserImportJob->deleteById($jobId);
		
		$newJob = $this->companyUserImportJob->byId($jobId);
		if ($newJob == null) {
			return array("success" => true);
		} else {
			$error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Job']);
			return response()->json($error)->setStatusCode($this->status_codes['conflict']);
		}
	}

	/*
	 * PRIVATE FUNCTIONS
	 */
	private function getFormatRow($header, $row) {
        $result = new \stdClass();
        foreach($header as $index => $field) {
            if(isset($row[$index])) {
                $result->$field = $row[$index];
            } else {
                $result->$field = '';
            }
        }

        return $result;
    }

    private function creatableOrUpdatable($rows) {
		$aux['creatableUsers'] = 0;
		$aux['updatableUsers'] = 0;
		$userInterface = app()->make('WA\Repositories\User\UserInterface');

        foreach($rows as $index => $row) {
            if($index == 0) continue;
            if(empty($row)) continue;
            if(join('', $row) == '') continue;
            $formattedRow = $this->getFormatRow($rows[0], $row);

            if (isset($formattedRow->companyUserIdentifier)) {
				$user = $userInterface->byCompanyIdentifier($formattedRow->companyUserIdentifier);

				if ($user != null) {
					$aux['updatableUsers']++;
				} else {
					$aux['creatableUsers']++;
				}

			} else if (isset($formattedRow->email)) {
				$user = $userInterface->byEmail($formattedRow->email);
				if ($user != null) {
					$aux['updatableUsers']++;
				} else {
					$aux['creatableUsers']++;
				}

			} else {
				// NOTHING
			}
        }

        return $aux;
    }

    private function retrieveDBFields($companyId) {
		$userInterface = app()->make('WA\Repositories\User\UserInterface');
		$userFields = $userInterface->getMappableFields();

		$companyInterface = app()->make('WA\Repositories\Company\CompanyInterface');
		$udlFields = $companyInterface->getMappableFields($companyId);

		return array_merge($userFields, $udlFields);
    }

    private function getMappingsFromOtherJobs($companyId) {
		$companyUIJInterface = app()->make('WA\Repositories\Company\CompanyUserImportJobInterface');
		$mappings = $companyUIJInterface->getMappingsByCompanyId($companyId);

		return $mappings;
    }

    private function getSampleUser($rows) {
    	$firstMeaningfulRow = "";

    	$position = 0;
    	foreach ($rows as $value) {
    		if ($position != 0) {
    			if(isset($value)) {
					$firstMeaningfulRow = $value;
					break;
				}
    		}

    		$position++; 		
    	}

    	if ($firstMeaningfulRow == '') {
    		return [];
    	}
		return array_combine($rows[0], $firstMeaningfulRow);
    }
}
