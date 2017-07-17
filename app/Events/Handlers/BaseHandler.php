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
        $user = \WA\DataStore\User\User::find($order->userId);
        $company = \WA\DataStore\Company\Company::find($user->companyId);

        $attributes['company']['name'] = $company->name;
        $attributes['order']['orderType'] = $order->orderType;

        foreach ($user->udlValues as $value) {
            if ($value->udlName == 'Department') {
                $attributes['company']['udl']['department'] = $value->udlValue;
            }

            if ($value->udlName == 'Cost Center') {
                $attributes['company']['udl']['costcenter'] = $value->udlValue;
            }
        }

        $attributes['activeuser']['name'] = \Auth::user()->username;
        $attributes['device']['mobilenumber'] = $order->servicePhoneNo;

        $address = $service = $package = $devicevariations = null;

        if (isset($order->addressId)) {
            $address = \WA\DataStore\Address\Address::find($order->addressId);
        }

        if (isset($order->serviceId)) {
            $service = \WA\DataStore\Service\Service::find($order->serviceId);
        }

        if (isset($order->packageId)) {
            $package = \WA\DataStore\Package\Package::find($order->packageId);
        }

        if (isset($order->devicevariations)) {
            $devicevariations = $order->devicevariations;
        }

        // USER ATTRIBUTES
        if (isset($user->username)) {
            $attributes['user']['username'] = $user->username;
        }
        if (isset($user->email)) {
            $attributes['user']['email'] = $user->email;
        }
        if (isset($user->supervisorEmail)) {
            $attributes['user']['supervisorEmail'] = $user->supervisorEmail;
        }

        // ADDRESS ATTRIBUTES
        if (isset($address->name)) {
            $attributes['address']['name'] = $address->name;
        }
        if (isset($address->address)) {
            $attributes['address']['address'] = $address->address;
        }
        if (isset($address->city)) {
            $attributes['address']['city'] = $address->city;
        }
        if (isset($address->state)) {
            $attributes['address']['state'] = $address->state;
        }
        if (isset($address->country)) {
            $attributes['address']['country'] = $address->country;
        }
        if (isset($address->postalCode)) {
            $attributes['address']['postalCode'] = $address->postalCode;
        }

        // PACKAGE ATTRIBUTES
        if(isset($package->name)) {
            $attributes['package']['name'] = $package->name;
        }

        // SERVICE ATTRIBUTES
        if(isset($service->title)) {
            $attributes['service']['title'] = $service->title;
            $attributes['service']['description'] = $service->description;
            $i = 0;
            foreach ($service->serviceitems as $si) {
                if($si->domain == 'domestic' && $si->category == 'voice') {
                    $attributes['service']['domesticvoice'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'domestic' && $si->category == 'data') {
                    $attributes['service']['domesticdata'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'domestic' && $si->category == 'messaging') {
                    $attributes['service']['domesticmess'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'international' && $si->category == 'voice') {
                    $attributes['service']['internationalvoice'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'international' && $si->category == 'data') {
                    $attributes['service']['internationaldata'] = $si['value'] . ' ' . $si['unit'];
                }

                if($si->domain == 'international' && $si->category == 'messaging') {
                    $attributes['service']['internationalmess'] = $si['value'] . ' ' . $si['unit'];
                }
            }
        }

        // DEVICEVARIATION ATTRIBUTES
        if (count($devicevariations) > 0) {
            $attributes['device']['accessories'] = '';
            foreach ($devicevariations as $dv) {
                if(isset($dv->devices)) {
                    if (isset($dv->devices->devicetypes)) {
                        if ($dv->devices->devicetypes->name == 'Smartphone') {
                            $attributes['device']['smartphone']['make'] = $dv->devices->make;
                            $attributes['device']['smartphone']['model'] = $dv->devices->model;
                            $attributes['device']['smartphone']['carrier'] = $dv->carriers->presentation;
                        }

                        if ($dv->devices->devicetypes->name == 'Accessory') {
                            if ($attributes['device']['accessories'] == '') {
                                $attributes['device']['accessories'] = $dv->devices->name;
                            } else {
                                $attributes['device']['accessories'] = $attributes['device']['accessories'] . ', ' . $dv->devices->name;
                            }
                        }
                    }
                }
            }
        }

        return $attributes;
    }

    protected function attributesToEasyVista($attributes) {

        $attributes['packageAC'] = isset($package->approvalCode) ? $package->approvalCode : env('EV_DEFAULT_APPROVAL_CODE');

        $attributes['email'] = isset($attributes['user']['email']) ? $attributes['user']['email'] : '';

        $attributes['description']  = View::make('emails.notifications.order.order_create_send_easyvista',
                [
                    'companyName' => isset($attributes['company']['name'])
                        ? $attributes['company']['name'] : '',
                    'orderType' => isset($attributes['order']['orderType'])
                        ? $attributes['order']['orderType'] : '',
                    'username' => isset($attributes['user']['username'])
                        ? $attributes['user']['username'] : '',
                    'packageName' => isset($attributes['package']['name'])
                        ? $attributes['package']['name'] : '',

                    'userEmail' => isset($attributes['user']['email'])
                        ? $attributes['user']['email'] : '',
                    'supervisorEmail' => isset($attributes['user']['supervisorEmail'])
                        ? $attributes['user']['supervisorEmail'] : '',
                    'udlDepartment' => isset($attributes['company']['udl']['department'])
                        ? $attributes['company']['udl']['department'] : '',
                    'udlCostCenter' => isset($attributes['company']['udl']['costcenter'])
                        ? $attributes['company']['udl']['costcenter'] : '',
                    'activeUser' => isset($attributes['activeuser']['name'])
                        ? $attributes['activeuser']['name'] : '',

                    'mobileNumber' => isset($attributes['device']['mobilenumber'])
                        ? $attributes['device']['mobilenumber'] : '',
                    'deviceCarrier' => isset($attributes['device']['smartphone']['carrier'])
                        ? $attributes['device']['smartphone']['carrier'] : '',
                    'deviceMake' => isset($attributes['device']['smartphone']['make'])
                        ? $attributes['device']['smartphone']['make'] : '',
                    'deviceModel' => isset($attributes['device']['smartphone']['model'])
                        ? $attributes['device']['smartphone']['model'] : '',
                    'deviceAccessories' => isset($attributes['device']['accessories'])
                        ? $attributes['device']['accessories'] : '',

                    'domesticvoice' => isset($attributes['service']['domesticvoice'])
                        ? $attributes['service']['domesticvoice'] : '',
                    'domesticdata' => isset($attributes['service']['domesticdata'])
                        ? $attributes['service']['domesticdata'] : '',
                    'domesticmessage' => isset($attributes['service']['domesticmess'])
                        ? $attributes['service']['domesticmess'] : '',
                    'internationalvoice' => isset($attributes['service']['internationalvoice'])
                        ? $attributes['service']['internationalvoice'] : '',
                    'internationaldata' => isset($attributes['service']['internationaldata'])
                        ? $attributes['service']['internationaldata'] : '',
                    'internationalmessage' => isset($attributes['service']['internationalmess'])
                        ? $attributes['service']['internationalmess'] : '',

                    'addressAddress' => isset($attributes['address']['address'])
                        ? $attributes['address']['address'] : '',
                    'addressCity' => isset($attributes['address']['city'])
                        ? $attributes['address']['city'] : '',
                    'addressState' => isset($attributes['address']['state'])
                        ? $attributes['address']['state'] : '',
                    'addressPostalCode' => isset($attributes['address']['postalCode'])
                        ? $attributes['address']['postalCode'] : '',

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
