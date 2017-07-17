<?php

/**
 * SendEVRequest - Gets the event received by the WorkflowEventSubscriber.
 *
 * @author Agustí Dosaiguas
 */
namespace WA\Events\Handlers;

/**
 * Class MainHandler.
 */
class SendEVRequest extends \WA\Events\Handlers\BaseHandler
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
        $events->listen('createTicketOnEasyVista', 'WA\Events\Handlers\SendEVRequest@createTicketOnEasyVista');
    }

    /**
     *  @param: $event
     */
    public function createTicketOnEasyVista($event) {
        
        try {
            $attributes = $this->retrieveTheAttributes($event->order);
            $attributes = $this->attributesToEasyVista($attributes);

            $easyVistaQueue = new \WA\Jobs\EasyVistaQueue($attributes);
            dispatch($easyVistaQueue);

            \Log::debug("SendEVRequest@createTicketOnEasyVista - EV Request has been queued.");
            return true;
        } catch (\Exception $e) {
            \Log::debug("SendEVRequest@createTicketOnEasyVista - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}