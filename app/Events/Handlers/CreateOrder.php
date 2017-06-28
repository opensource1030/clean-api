<?php

/**
 * CreateOrder - Gets the event received by the Create Order Enpoint.
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
        $events->listen('createOrderEmails', 'WA\Events\Handlers\CreateOrder@createOrderEmails');
    }

    public function createOrderEmails($event) {
        \Log::debug("CreateOrder@createOrderEmails");
        $workflow = \Workflow::get($event->order);
        $workflow->apply($event->order, 'create');
        $event->order->save();
    }
}
