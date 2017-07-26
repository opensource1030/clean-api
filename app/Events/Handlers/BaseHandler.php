<?php

namespace WA\Events\Handlers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\View;

/**
 * Class BaseHandler.
 */
abstract class BaseHandler
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        // No main subscriptions yet, should be declared in children
    }

    protected function retrieveTheAttributes($order)
    {
        $attributes = [];

        $attributes['if']['showCurrentService'] = false;
        $attributes['if']['showCurrentDevice'] = false;
        $attributes['if']['showNewService'] = false;
        $attributes['if']['showNewDevice'] = false;

        // VARIABLES
        // ORDER
        $attributes['order']['orderType'] = $order->orderType;

        // USER
        $attributes['user']['username'] = '';
        $attributes['user']['userEmail'] = '';
        $attributes['user']['supervisorEmail'] = '';
        $attributes['user']['activeUser'] = \Auth::user()->username;
        $attributes['user']['udlDepartment'] = '';
        $attributes['user']['udlCostCenter'] = '';

        $user = \WA\DataStore\User\User::find($order->userId);
        $attributes['user']['username'] = $user->username;
        $attributes['user']['userEmail'] = $user->email;
        $attributes['user']['supervisorEmail'] = $user->supervisorEmail;

        foreach ($user->udlValues as $value) {
            if ($value->udlName == 'Department') {
                $attributes['company']['udlDepartment'] = $value->udlValue;
            }
            if ($value->udlName == 'Cost Center') {
                $attributes['company']['udlCostCenter'] = $value->udlValue;
            }
        }

        // PACKAGE
        $attributes['package']['packageName'] = '';
        $attributes['package']['approvalCode'] = '';

        if ($order->packageId != null && $order->packageId > 0) {
            $package = \WA\DataStore\Package\Package::find($order->packageId);

            $attributes['package']['packageName'] = $package->packageName;
            $attributes['package']['approvalCode'] = $package->approvalCode;
        }

        // CURRENT SERVICE
        $attributes['service']['serviceImei'] = $order->serviceImei;
        $attributes['service']['servicePhoneNo'] = $order->servicePhoneNo;
        $attributes['service']['serviceSim'] = $order->serviceSim;

        if ($attributes['service']['serviceImei'] != '' ||
            $attributes['service']['servicePhoneNo'] != '' ||
            $attributes['service']['serviceSim'] != '') {
            $attributes['if']['showCurrentService'] = true;
        }

        // CURRENT DEVICE
        $attributes['device']['deviceImei'] = $order->deviceImei;
        $attributes['device']['deviceCarrier'] = $order->deviceCarrier;
        $attributes['device']['deviceSim'] = $order->deviceSim;

        if ($attributes['device']['deviceImei'] != '' ||
            $attributes['device']['deviceCarrier'] != '' ||
            $attributes['device']['deviceSim'] != '') {
            $attributes['if']['showCurrentDevice'] = true;
        }

        // SERVICE
        $attributes['service']['domesticvoice'] = '';
        $attributes['service']['domesticdata'] = '';
        $attributes['service']['domesticmessage'] = '';
        $attributes['service']['internationalvoice'] = '';
        $attributes['service']['internationaldata'] = '';
        $attributes['service']['internationalmessage'] = '';

        if ($order->serviceId != null && $order->serviceId > 0) {
            $service = \WA\DataStore\Service\Service::find($order->serviceId);

            foreach ($service->serviceitems as $si) {
                if($si->domain == 'domestic' && $si->category == 'voice') {
                    $attributes['service']['domesticvoice'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'domestic' && $si->category == 'data') {
                    $attributes['service']['domesticdata'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'domestic' && $si->category == 'messaging') {
                    $attributes['service']['domesticmessage'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'international' && $si->category == 'voice') {
                    $attributes['service']['internationalvoice'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'international' && $si->category == 'data') {
                    $attributes['service']['internationaldata'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'international' && $si->category == 'messaging') {
                    $attributes['service']['internationalmessage'] = $si['value'] . ' ' . $si['unit'];
                }
            }

            if ($attributes['service']['domesticvoice'] != '' ||
                $attributes['service']['domesticdata'] != '' ||
                $attributes['service']['domesticmessage'] != '' ||
                $attributes['service']['internationalvoice'] != '' ||
                $attributes['service']['internationaldata'] != '' ||
                $attributes['service']['internationalmessage'] != '') {
                $attributes['if']['showNewService'] = true;
            }
        }


        // DEVICE
        $attributes['device']['devicePhoneNo'] = 'No Info Provided';
        $attributes['device']['deviceMake'] = '';
        $attributes['device']['deviceModel'] = '';
        $attributes['device']['deviceAccessories'] = '';

        if (isset($order->devicevariations)) {
            $devicevariations = $order->devicevariations;

            if (count($devicevariations) > 0) {
                foreach ($devicevariations as $dv) {
                    if(isset($dv->devices)) {
                        if (isset($dv->devices->devicetypes)) {
                            if ($dv->devices->devicetypes->name == 'Smartphone') {
                                $attributes['device']['deviceMake'] = $dv->devices->make;
                                $attributes['device']['deviceModel'] = $dv->devices->model;
                            }

                            if ($attributes['device']['deviceMake'] != '' ||
                                $attributes['device']['deviceModel'] != '') {
                                $attributes['if']['showNewDevice'] = true;
                            }

                            if ($dv->devices->devicetypes->name == 'Accessory') {
                                if ($attributes['device']['deviceAccessories'] == '') {
                                    $attributes['device']['deviceAccessories'] = $dv->devices->name;
                                } else {
                                    $attributes['device']['deviceAccessories'] = $attributes['device']['deviceAccessories'] . ', ' . $dv->devices->name;
                                }
                            }
                        }
                    }
                }
            }
        }

        // COMPANY
        $attributes['company']['companyName'] = '';

        if ($user->companyId != null && $user->companyId > 0) {
            $company = \WA\DataStore\Company\Company::find($user->companyId);

            $attributes['company']['companyName'] = $company->name;
        }

        // ADDRESS
        $attributes['address']['addressAddress'] = '';
        $attributes['address']['addressCity'] = '';
        $attributes['address']['addressState'] = '';
        $attributes['address']['addressPostalCode'] = '';

        if ($order->addressId != null && $order->addressId > 0) {
            $address = \WA\DataStore\Address\Address::find($order->addressId);

            $attributes['address']['addressAddress'] = $address->address;
            $attributes['address']['addressCity'] = $address->city;
            $attributes['address']['addressState'] = $address->state;
            $attributes['address']['addressPostalCode'] = $address->postalCode;
        }

        \Log::debug("BaseHandler@retrieveTheAttributes - attributes: " . print_r($attributes, true));
        return $attributes;
    }

    protected function attributesToEasyVista($attributes) {

        $attributes['packageAC'] = isset($package->approvalCode) ? $package->approvalCode : env('EV_DEFAULT_APPROVAL_CODE');

        $attributes['email'] = isset($attributes['user']['email']) ? $attributes['user']['email'] : '';

        $attributes['description']  = View::make('emails.notifications.order.order_create_send_easyvista',
                [
                    'companyName' => isset($attributes['company']['companyName'])
                        ? $attributes['company']['companyName'] : '',
                    'orderType' => isset($attributes['order']['orderType'])
                        ? $attributes['order']['orderType'] : '',
                    'username' => isset($attributes['user']['username'])
                        ? $attributes['user']['username'] : '',
                    'packageName' => isset($attributes['package']['packageName'])
                        ? $attributes['package']['packageName'] : '',

                    'userEmail' => isset($attributes['user']['userEmail'])
                        ? $attributes['user']['userEmail'] : '',
                    'supervisorEmail' => isset($attributes['user']['supervisorEmail'])
                        ? $attributes['user']['supervisorEmail'] : '',
                    'udlDepartment' => isset($attributes['user']['udlDepartment'])
                        ? $attributes['user']['udlDepartment'] : '',
                    'udlCostCenter' => isset($attributes['user']['udlCostCenter'])
                        ? $attributes['user']['udlCostCenter'] : '',
                    'activeUser' => isset($attributes['user']['activeUser'])
                        ? $attributes['user']['activeUser'] : '',

                    'mobileNumber' => isset($attributes['device']['devicePhoneNo'])
                        ? $attributes['device']['devicePhoneNo'] : '',
                    'deviceCarrier' => isset($attributes['device']['smartphone']['deviceCarrier'])
                        ? $attributes['device']['smartphone']['deviceCarrier'] : '',
                    'deviceMake' => isset($attributes['device']['smartphone']['deviceMake'])
                        ? $attributes['device']['smartphone']['deviceMake'] : '',
                    'deviceModel' => isset($attributes['device']['smartphone']['deviceModel'])
                        ? $attributes['device']['smartphone']['deviceModel'] : '',
                    'deviceAccessories' => isset($attributes['device']['deviceAccessories'])
                        ? $attributes['device']['deviceAccessories'] : '',

                    'domesticvoice' => isset($attributes['service']['domesticvoice'])
                        ? $attributes['service']['domesticvoice'] : '',
                    'domesticdata' => isset($attributes['service']['domesticdata'])
                        ? $attributes['service']['domesticdata'] : '',
                    'domesticmessage' => isset($attributes['service']['domesticmessage'])
                        ? $attributes['service']['domesticmessage'] : '',
                    'internationalvoice' => isset($attributes['service']['internationalvoice'])
                        ? $attributes['service']['internationalvoice'] : '',
                    'internationaldata' => isset($attributes['service']['internationaldata'])
                        ? $attributes['service']['internationaldata'] : '',
                    'internationalmessage' => isset($attributes['service']['internationalmessage'])
                        ? $attributes['service']['internationalmessage'] : '',

                    'addressAddress' => isset($attributes['address']['addressAddress'])
                        ? $attributes['address']['addressAddress'] : '',
                    'addressCity' => isset($attributes['address']['addressCity'])
                        ? $attributes['address']['addressCity'] : '',
                    'addressState' => isset($attributes['address']['addressState'])
                        ? $attributes['address']['addressState'] : '',
                    'addressPostalCode' => isset($attributes['address']['addressPostalCode'])
                        ? $attributes['address']['addressPostalCode'] : '',

                ]
            )->render();

        return $attributes;
    }

    protected function retrieveEmail($email) {

        if (env('MAIL_USERNAME') != null) {
            return env('MAIL_USERNAME');
        } else {
            return $email;
        }
    }
}
