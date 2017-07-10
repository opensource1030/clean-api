<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Address\Address;
use WA\DataStore\Address\AddressTransformer;
use WA\Repositories\Address\AddressInterface;

use DB;

/**
 * Address resource.
 *
 * @Resource("address", uri="/address")
 */
class AddressController extends FilteredApiController
{
    /**
     * @var AddressInterface
     */
    protected $address;

    /**
     * Address Controller constructor.
     *
     * @param AddressInterface $address
     * @param Request $request
     */
    public function __construct(AddressInterface $address, Request $request)
    {
        parent::__construct($address, $request);
        $this->address = $address;
    }

    /**
     * Update contents of an Address.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'addresses')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if(!$this->addFilterToTheRequest("store", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $address = $this->address->update($data['attributes']);

            if ($address == 'notExist') {
                DB::rollBack();
                $error['errors']['address'] = Lang::get('messages.NotExistClass', ['class' => 'Address']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($address == 'notSaved') {
                DB::rollBack();
                $error['errors']['address'] = Lang::get('messages.NotSavedClass', ['class' => 'Address']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['addresses'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'address', 'option' => 'updated', 'include' => '']);
            $error['errors']['addressesMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['users'])) {
                if (isset($data['relationships']['users']['data'])) {
                    try {
                        $dataUsers = $this->parseJsonToArray($data['relationships']['users']['data'], 'users');
                        $address->users()->sync($dataUsers);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['users'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Address', 'option' => 'created', 'include' => 'Users']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if (isset($data['relationships']['companies'])) {
                if (isset($data['relationships']['companies']['data'])) {
                    try {
                        $dataCompanies = $this->parseJsonToArray($data['relationships']['companies']['data'], 'companies');
                        $address->companies()->sync($dataCompanies);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Address', 'option' => 'created', 'include' => 'Companies']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if (isset($data['relationships']['packages'])) {
                if (isset($data['relationships']['packages']['data'])) {
                    try {
                        $datapackages = $this->parseJsonToArray($data['relationships']['packages']['data'], 'packages');
                        $address->packages()->sync($datapackages);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['packages'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Address', 'option' => 'created', 'include' => 'Packages']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($address, new AddressTransformer(),
            ['key' => 'addresses'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new Address.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'addresses')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if(!$this->addFilterToTheRequest("store", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data'];
            $address = $this->address->create($data['attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['addresses'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'address', 'option' => 'updated', 'include' => '']);
            $error['errors']['addressesMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['users'])) {
                if (isset($data['relationships']['users']['data'])) {
                    try {
                        $dataUsers = $this->parseJsonToArray($data['relationships']['users']['data'], 'users');
                        $address->users()->sync($dataUsers);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['users'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Address', 'option' => 'created', 'include' => 'Users']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if (isset($data['relationships']['companies'])) {
                if (isset($data['relationships']['companies']['data'])) {
                    try {
                        $dataCompanies = $this->parseJsonToArray($data['relationships']['companies']['data'], 'companies');
                        $address->companies()->sync($dataCompanies);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Address', 'option' => 'created', 'include' => 'Companies']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if (isset($data['relationships']['packages'])) {
                if (isset($data['relationships']['packages']['data'])) {
                    try {
                        $datapackages = $this->parseJsonToArray($data['relationships']['packages']['data'], 'packages');
                        $address->packages()->sync($datapackages);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['packages'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Address', 'option' => 'created', 'include' => 'Packages']);
                        $error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($address, new AddressTransformer(),
            ['key' => 'addresses'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a Address.
     *
     * @param $id
     */
    public function delete($id)
    {
        if(!$this->addFilterToTheRequest("delete", null)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $address = Address::find($id);
        if ($address != null) {
            $this->address->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Address']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $address = Address::find($id);
        if ($address == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Address']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
