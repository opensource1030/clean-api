<?php

namespace WA\Events\Handlers;

use Illuminate\Events\Dispatcher;

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
        $user = \WA\DataStore\User\User::find($order->userId);
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

        $attributes = [];

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
            foreach ($devicevariations as $dv) {
                if(isset($dv->devices)) {
                    if (isset($dv->devices->devicetypes)) {
                        $attributes['device']['deviceInfo'] = 'The user\'s own device';
                        if ($dv->devices->devicetypes->name == 'Smartphone') {
                            $attributes['device']['deviceInfo'] = $dv->devices->name . ' : ' . $dv->devices->defaultPrice . ' ' . $dv->devices->currency;
                        }
                    }
                }
            }
        }

        return $attributes;
    }

    protected function easyVistaStringDescription($order)
    {
        $user = \WA\DataStore\User\User::find($order->userId);
        $attributes['email'] = $user->email;

        $address = \WA\DataStore\Address\Address::find($order->addressId);

        $service = \WA\DataStore\Service\Service::find($order->serviceId);

        $package = \WA\DataStore\Package\Package::find($order->packageId);
        $attributes['packageAC'] = isset($package->approvalCode) ? $package->approvalCode : '';

        $devicevariations = $order->devicevariations;

        $company = \WA\DataStore\Company\Company::find($user->companyId);

        $description = '';

        // Company.
        $description = $description .
            '<h2><strong>' . $company->name .
            ' - ' . $order->orderType .
            ' - ' . $user->username .
            '</strong></h2>';

        $packageName = isset($package->name) ? $package->name : '';

        // Package.
        $description = $description .
            '<h3><strong>Package Name: </strong>' . $packageName .
            '</h3>';

        $description = $description .
            '<hr />';

        // User
        $departmentUdl = '';
        $costCenterUdl = '';
        $udlValues = $user->udlvalues;
        foreach ($udlValues as $udlValue) {
            $udl = \WA\DataStore\Udl\Udl::find($udlValue->udlId);
            if ($udl->name == 'Department') {
                $departmentUdl = $udlValue->name;
            }

            if ($udl->name == 'Cost Center') {
                $costCenterUdl = $udlValue->name;
            }
        }

        $activeLogin = \Auth::user();
        $description = $description .
        '<h3 class="heading2">User Info:</h3>' .
        '<p>' .
            '<strong>Username:</strong>&nbsp;' . $user->username .
            '<br /><strong>Email:</strong>&nbsp;' . $user->email .
            '<br /><strong>Supervisor Email:</strong> ' . $user->supervisorEmail .
            '<br /><strong>Department:</strong> ' . $departmentUdl .
            '<br /><strong>Cost Center:</strong> ' . $costCenterUdl .
        '</p>' .
        '<p>' .
            '<strong>Entered by:</strong>&nbsp;' . $activeLogin->username .
        '</p>';

        $description = $description .
            '<hr />';

        $smartphone = '';
        $accessories = '';
        if ($devicevariations == null) {
            foreach ($devicevariations as $dv) {
                if ($dv->devices->devicetypes->name == 'Smartphone') {
                    $smartphone = $dv;
                }

                if ($dv->devices->devicetypes->name == 'Accessory') {
                    if ($accessories == '') {
                        $accessories = $accessories . ', ';
                    }
                    $accessories = $accessories . $dv->name;
                }
            }
        }

        if ($smartphone == '') {
            $make = '';
            $model = '';
        } else {
            $make = $smartphone->devices->make;
            $model = $smartphone->devices->model;
        }

        $domVo = $domDa = $domMe = $intVo = $intDa = $intMe = '';
        if ($service == null) {
            $CarrierName = $order->deviceCarrier;
        } else {
            $CarrierName = $service->carriers->name;

            foreach ($service->serviceitems as $si) {
                if ($si->domain == 'domestic') {
                    if ($si->category == 'voice') {
                        $domVo = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'data') {
                        $domDa = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'messages') {
                        $domMe = $si->value . ' ' . $si->unit;
                    } else {
                        // NOTHING.
                    }
                } else if ($si->domain == 'international') {
                    if ($si->category == 'voice') {
                        $intVo = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'data') {
                        $intDa = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'messages') {
                        $intMe = $si->value . ' ' . $si->unit;
                    } else {
                        // NOTHING.
                    }
                }
            }
        }

        //
        $description = $description .
            '<h3 class="heading2">Device&nbsp;Info:</h3>' .
            '<p>' .
                '<strong>Mobile Number:</strong> ' . $order->servicePhoneNo .
                '<br />' .
                '<strong>Carrier:</strong> ' . $CarrierName .
                '<br />' .
                '<strong>Make/Model:</strong> ' . $make . ' ' . $model .
                '<br />' .
                '<strong>Accessories:</strong> ' . $accessories .
            '</p>';

        $description = $description .
            '<hr />';

        $description = $description .
            '<h3 class="heading2">Mobile Service Info:</h3>' .
            '<p>' .
                '<strong>Domestic Voice:</strong>' . $domVo .
                '<br />' .
                '<strong>Domestic Data:</strong>' . $domDa .
                '<br />' .
                '<strong>Domestic Messaging:</strong>' . $domMe .
                '<br />' .
                '<strong>International Voice:</strong>' . $intVo .
                '<br />' .
                '<strong>International Data:</strong>' . $intDa .
                '<br />' .
                '<strong>International Messaging:</strong>' . $intMe .
            '</p>';

        $description = $description .
            '<hr />';

        $description = $description .
            '<h3 class="heading2">Shipping Info:</h3>' .
            '<p>' . $company->name .
                '<br />' . $address->name .
                '<br />' . $address->city . ', ' . $address->state . ', ' . $address->postalCode .
                '<br />Attn.&nbsp;' . $user->username .
            '</p>';
/*
        $description = $description .
            '<hr />';

        $description = $description .
            '<h3 class="heading2">Comments:</h3><p>Open comments field.</p>';
*/

        $attributes['description'] = $description;
        return $attributes;
    }
}
