<?php

/**
 * MainHandler - Gets the event received by the Single Sign On.
 *
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use WA\Events\PodcastWasPurchased;

/**
 * Class MainHandler.
 */
class CreateOrder extends \WA\Events\Handlers\BaseHandler
{
    protected $order;

    /**
     * Create a new event instance.
     */
    public function __construct(\WA\DataStore\Order\Order $order)
    {
        $this->order = $order;
    }
    /**
     * @param Dispatcher $events
     */
    public function handle(Dispatcher $events)
    {
        $events->listen('sendOrderConfirmationEmail', 'WA\Events\Handlers\EmailEvent@sendOrderConfirmationEmail');
    }

    /**
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        try {
            //$userOrder = Auth::user();
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);

            $address = $service = $package = $devicevariations = null;

            if (isset($event->order->addressId)) {
                $address = \WA\DataStore\Address\Address::find($event->order->addressId);    
            }
            
            if (isset($event->order->serviceId)) {
                $service = \WA\DataStore\Service\Service::find($event->order->serviceId);    
            }

            if (isset($event->order->packageId)) {
                $package = \WA\DataStore\Package\Package::find($event->order->packageId);    
            }

            if (isset($event->order->devicevariations)) {
                $devicevariations = $event->order->devicevariations;
            }

            $attributes = $this->retrieveTheAttributes($userOrder, $address, $package, $service, $devicevariations);
            //\Log::debug("OrdersController@sendConfirmationEmail - attributes: " . print_r($attributes, true));

            $adminRole = \WA\DataStore\Role\Role::where('name', 'admin')->first();
            $listOfAdmins = \WA\DataStore\User\UserRole::where('role_id', $adminRole->id)->get();

            foreach ($listOfAdmins as $admin) {
                $adminRetrieved = \WA\DataStore\User\User::find($admin->user_id);

                if ($adminRetrieved->companyId == $userOrder->companyId) {
                    $resAdmin = \Illuminate\Support\Facades\Mail::send(
                        'emails.notifications.new_order_received', // VIEW NAME
                        [
                            'username' => 'adminMessage',//$userOrder->username,
                            'redirectPath' => 'urlderedireccion'
                        ], // PARAMETERS PASSED TO THE VIEW
                        function ($message) {
                            $message->subject('New Order Received.');
                            $message->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                            $message->to('didac.pallares@siriondev.com');//$adminRetrieved->email);
                        } // CALLBACK
                    );
                }
            }

            $domesticvoice = $domesticdata = $domesticmess = $internationalvoice = $internationaldata = $internationalmess = 'No data Provided';
            if(isset($attributes['service'])) {
                foreach ($attributes['service']['serviceitems'] as $si) {
                    if($si['domain'] == 'domestic' && $si['category'] == 'voice') {
                        $domesticvoice = title_case($si['domain']) . ' - ' . title_case($si['category']) . ' : ' . $si['value'] . ' ' . $si['unit'];
                    }

                    if($si['domain'] == 'domestic' && $si['category'] == 'data') {
                        $domesticdata = title_case($si['domain']) . ' - ' . title_case($si['category']) . ' : ' . $si['value'] . ' ' . $si['unit'];
                    }

                    if($si['domain'] == 'domestic' && $si['category'] == 'messaging') {
                        $domesticmess = title_case($si['domain']) . ' - ' . title_case($si['category']) . ' : ' . $si['value'] . ' ' . $si['unit'];
                    }

                    if($si['domain'] == 'international' && $si['category'] == 'voice') {
                        $internationalvoice = title_case($si['domain']) . ' - ' . title_case($si['category']) . ' : ' . $si['value'] . ' ' . $si['unit'];
                    }

                    if($si['domain'] == 'international' && $si['category'] == 'data') {
                        $internationaldata = title_case($si['domain']) . ' - ' . title_case($si['category']) . ' : ' . $si['value'] . ' ' . $si['unit'];
                    }

                    if($si['domain'] == 'international' && $si['category'] == 'messaging') {
                        $internationalmess = title_case($si['domain']) . ' - ' . title_case($si['category']) . ' : ' . $si['value'] . ' ' . $si['unit'];
                    }
                }
            }

            $deviceInfo = 'The user\'s own device';
            if (isset($attributes['device'])) {
                $deviceInfo = $attributes['device']['name'] . ' : ' . $attributes['device']['defaultPrice'] . ' ' . $attributes['device']['currency'];
            }

            $resUser = \Illuminate\Support\Facades\Mail::send(
                'emails.notifications.new_order_created', // VIEW NAME
                [
                    'username' => $userOrder->username,
                    'redirectPath' => 'urlderedireccion',
                    'username' => isset($attributes['user']['username']) ? $attributes['user']['username'] : '',
                    'useremail' => isset($attributes['user']['email']) ? $attributes['user']['email'] : '',
                    'usersupervisoremail' => isset($attributes['user']['supervisorEmail']) ? $attributes['user']['supervisorEmail'] : '',
                    'addressname' => isset($attributes['address']['name']) ? $attributes['address']['name'] : '',
                    'addresscity' => isset($attributes['address']['city']) ? $attributes['address']['city'] : '',
                    'addressstate' => isset($attributes['address']['state']) ? $attributes['address']['state'] : '',
                    'addresscountry' => isset($attributes['address']['country']) ? $attributes['address']['country'] : '',
                    'packagename' => isset($attributes['package']['name']) ? $attributes['package']['name'] : '',
                    'servicetitle' => isset($attributes['service']['title']) ? $attributes['service']['title'] : '',
                    'serviceitemsdomvo' => $domesticvoice,
                    'serviceitemsdomdata' => $domesticdata,
                    'serviceitemsdommess' => $domesticmess,
                    'serviceitemsintvo' => $internationalvoice,
                    'serviceitemsintdata' => $internationaldata,
                    'serviceitemsintmess' => $internationalmess,
                    'deviceinfo' => $deviceInfo
                ], // PARAMETERS PASSED TO THE VIEW
                function ($message) use ($userOrder) {
                    $message->subject('New Order Created.');
                    $message->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                    $message->to('didac.pallares@siriondev.com');//$userOrder->email);
                } // CALLBACK
            );
            \Log::debug("OrdersController@sendConfirmationEmail - All Emails have been sent.");
        } catch (\Exception $e) {
            \Log::debug("OrdersController@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }

    private function retrieveTheAttributes($user, $address, $package, $service, $devicevariations) {
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
        if (isset($address->city)) {
            $attributes['address']['city'] = $address->city;
        }
        if (isset($address->state)) {
            $attributes['address']['state'] = $address->state;
        }
        if (isset($address->country)) {
            $attributes['address']['country'] = $address->country;
        }

        // PACKAGE ATTRIBUTES
        if(isset($package->name)) {
            $attributes['package']['name'] = $package->name;
        }

        // SERVICE ATTRIBUTES
        if(isset($service->title)) {
            $attributes['service']['title'] = $service->title;
            $i = 0;
            foreach ($service->serviceitems as $si) {
                if ($si->domain == 'domestic' || $si->domain == 'international') {
                    if ($si->value > 0) {
                        if (isset($si->domain)) {
                            $attributes['service']['serviceitems'][$i]['domain'] = $si->domain;
                        }

                        if (isset($si->category)) {
                            $attributes['service']['serviceitems'][$i]['category'] = $si->category;
                        }

                        if (isset($si->value)) {
                            $attributes['service']['serviceitems'][$i]['value'] = $si->value;
                        }

                        if (isset($si->unit)) {
                            $attributes['service']['serviceitems'][$i]['unit'] = $si->unit;
                        }
                    }
                }
                $i++;
            }
        }

        // DEVICEVARIATION ATTRIBUTES
        if (count($devicevariations) > 0) {
            foreach ($devicevariations as $dv) {
                if(isset($dv->devices)) {
                    if (isset($dv->devices->devicetypes)) {
                        if ($dv->devices->devicetypes->name == 'Smartphone') {
                            if (isset($dv->devices->name)) {
                                $attributes['device']['name'] = $dv->devices->name;
                            }

                            if (isset($dv->devices->defaultPrice)) {
                                $attributes['device']['defaultPrice'] = $dv->devices->defaultPrice;
                            }

                            if (isset($dv->devices->currency)) {
                                $attributes['device']['currency'] = $dv->devices->currency;
                            }

                            if (isset($dv->devices->property)) {
                                $attributes['device']['property'] = $dv->devices->property;
                            }
                        }
                    }
                }
            }
        }

        return $attributes;
    }

    private function convertObjectToHtmlString($object) {

        $string = '';

        if (isset($object['user'])) {
            if (isset($object['user']['username'])) {
                if (isset($object['user']['email'])) {
                    if (isset($object['user']['supervisorEmail'])) {
                        // Username + email + supervisorEmail
                        $string = $string .
                            '<bold>Username:</bold> ' . $object['user']['username'] .
                            ' with email: ' . $object['user']['email'] .
                            ', with supervisor: ' . $object['user']['supervisorEmail'] . '<br>';
                    } else {
                        // Username + email
                        $string = $string .
                            '<bold>Username:</bold> ' . $object['user']['username'] .
                            ' with email: ' . $object['user']['email'] . '<br>';
                    }
                } else {
                    if (isset($object['user']['supervisorEmail'])) {
                        // Username + supervisorEmail
                        $string = $string .
                            '<bold>Username:</bold> ' . $object['user']['username'] .
                            ', with supervisor: ' . $object['user']['supervisorEmail'] . '<br>';
                    } else {
                        // Username
                        $string = $string .
                            '<bold>Username:</bold> ' . $object['user']['username'] . '<br>';
                    }
                }
            } else {
                if (isset($object['user']['email'])) {
                    if (isset($object['user']['supervisorEmail'])) {
                        // email + supervisorEmail
                        $string = $string .
                            '<bold>Email:</bold> ' . $object['user']['email'] .
                            ', with supervisor: ' . $object['user']['supervisorEmail'] . '<br>';
                    } else {
                        // email
                        $string = $string .
                            '<bold>Email:</bold> ' . $object['user']['email'] . '<br>';
                    }
                } else {
                    if (isset($object['user']['supervisorEmail'])) {
                        // supervisorEmail
                        $string = $string .
                            '<bold>Supervisor Email:</bold> ' . $object['user']['supervisorEmail'] . '<br>';
                    } else {
                        // No Information
                        $string = $string .
                            '<bold>No User Information Provided</bold><br>';
                    }
                }
            }
        }

        if (isset($object['address'])) {
            if (isset($object['address']['name'])) {
                if (isset($object['address']['city'])) {
                    if (isset($object['address']['state'])) {
                        if (isset($object['address']['country'])) {
                            // name + city + state + country
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' from: ' . $object['address']['city'] .
                                ' ( ' . $object['address']['state'] . 
                                ' - ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // name + city + state
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' from: ' . $object['address']['city'] .
                                ' ( ' . $object['address']['state']. ' )<br>';
                        }
                    } else {
                        if (isset($object['address']['country'])) {
                            // name + city + country
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' from: ' . $object['address']['city'] .
                                ' ( ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // name + city
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' from: ' . $object['address']['city'] . '<br>';
                        }
                    }
                } else {
                    if (isset($object['address']['state'])) {
                        if (isset($object['address']['country'])) {
                            // name + state + country
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' ( ' . $object['address']['state'] . 
                                ' - ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // name + state
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' ( ' . $object['address']['state']. ' )<br>';
                        }
                    } else {
                        if (isset($object['address']['country'])) {
                            // name + country
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] .
                                ' ( ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // name
                            $string = $string .
                                '<bold>Address:</bold> ' . $object['address']['name'] . '<br>';
                        }
                    }
                }
            } else {
                if (isset($object['address']['city'])) {
                    if (isset($object['address']['state'])) {
                        if (isset($object['address']['country'])) {
                            // city + state + country
                            $string = $string .
                                '<bold>Address from:</bold> ' . $object['address']['city'] .
                                ' ( ' . $object['address']['state'] .
                                ' - ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // city + state
                            $string = $string .
                                '<bold>Address from:</bold> ' . $object['address']['city'] .
                                ' ( ' . $object['address']['state']. ' )<br>';
                        }
                    } else {
                        if (isset($object['address']['country'])) {
                            // city + country
                            $string = $string .
                                '<bold>Address from:</bold> ' . $object['address']['city'] .
                                ' ( ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // city
                            $string = $string .
                                '<bold>Address from:</bold> ' . $object['address']['city'] . '<br>';
                        }
                    }
                } else {
                    if (isset($object['address']['state'])) {
                        if (isset($object['address']['country'])) {
                            // state + country
                            $string = $string .
                                '<bold>Address from:</bold>' . 
                                ' ( ' . $object['address']['state'] .
                                ' - ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // state
                            $string = $string .
                                '<bold>Address from:</bold>' . ' ( ' . $object['address']['state']. ' )<br>';
                        }
                    } else {
                        if (isset($object['address']['country'])) {
                            // country
                            $string = $string .
                                '<bold>Address:</bold>' . ' ( ' . $object['address']['country'] . ' )<br>';
                        } else {
                            // No Information
                            $string = $string .
                            '<bold>No Address Information Provided</bold><br>';
                        }
                    }
                }
            }
        }

        if (isset($object['package'])) {
            $string = $string . '<bold>Package Name:</bold> ' . $object['package']['name'] . '<br>';
        }        

        if (isset($object['service'])) {
            $string = $string . '<bold>Service Name:</bold> ' . $object['service']['title'] . '<br>';
            if (isset($object['service']['serviceitems'])) {
                foreach ($object['service']['serviceitems'] as $si) {
                    $string = $string . $si['domain'] .
                        ' - ' . $si['category'] .
                        ' : ' . $si['value'] .
                        ' ' . $si['unit'] . '<br>';
                }
            }
        }

        if (isset($object['device'])) {
            $string = $string .
                '<bold>Device Name</bold>: ' . $object['device']['name'] . ' - ' .
                $object['device']['defaultPrice'] . ' ' .
                $object['device']['currency'] . '<br>';
        }

        return $string;
    }
}
