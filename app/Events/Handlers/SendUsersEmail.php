<?php

/**
 * CreateOrder - Gets the event received by the Single Sign On.
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
class SendUsersEmail extends \WA\Events\Handlers\BaseHandler
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
        $events->listen('sendOrderConfirmationEmail', 'WA\Events\Handlers\SendUsersEmail@sendOrderConfirmationEmail');
    }

    /**
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        
        $order = $event->order;
        
        try {
            $userOrder = \WA\DataStore\User\User::find($order->userId);
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
}