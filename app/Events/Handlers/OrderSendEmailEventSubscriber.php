<?php

namespace WA\Events\Handlers;

class OrderSendEmailEventSubscriber
{
    /**
     * Handle workflow guard events.
     */
    public function onGuard(\Brexis\LaravelWorkflow\Events\GuardEvent $event) {
        \Log::debug("OrderSendEmailEventSubscriber@onGuard");
        /** Symfony\Component\Workflow\Event\GuardEvent */
        $originalEvent = $event->getOriginalEvent();

        /** @var App\BlogPost $post */
        $post = $originalEvent->getSubject();
        $status = $post->status;
        $userId = $post->userId;
        $addressId = $post->addressId;

        if (empty($status) || empty($userId) || empty($addressId)) {
            // Posts with no title should not be allowed
            $originalEvent->setBlocked(true);
        }
    }

    /**
     * Handle workflow leave event.
     */
    public function onLeave($event) {
        \Log::debug("OrderSendEmailEventSubscriber@onLeave");
    }

    /**
     * Handle workflow transition event.
     */
    public function onTransition($event) {
        \Log::debug("OrderSendEmailEventSubscriber@onTransition");
        
        $nameWorkflow = $event->getOriginalEvent()->getWorkflowName();
        $nameTransition = $event->getOriginalEvent()->getTransition()->getName();
        $order = $event->getOriginalEvent()->getSubject();
        
        switch ($nameWorkflow) {
            case 'new_order':
                switch ($nameTransition) {
                    case 'create':
                        \Log::debug("OrderSendEmailEventSubscriber@onTransition - create");
                        event(new \WA\Events\Handlers\SendEVEmail($order));
                        event(new \WA\Events\Handlers\SendUserEmailCreateOrder($order));
                        event(new \WA\Events\Handlers\SendAdminEmailCreateOrder($order));
                        break;
                    case 'accept':
                        \Log::debug("OrderSendEmailEventSubscriber@onTransition - accept");
                        event(new \WA\Events\Handlers\SendUserEmailOrderAccepted($order));
                        break;
                    case 'deny':
                        \Log::debug("OrderSendEmailEventSubscriber@onTransition - deny");
                        event(new \WA\Events\Handlers\SendUserEmailOrderDenied($order));
                        break;
                    case 'send':
                        \Log::debug("OrderSendEmailEventSubscriber@onTransition - send");
                        event(new \WA\Events\Handlers\SendUserEmailOrderDelivered($order));
                        break;
                    
                    default:
                        // NOTHING
                        break;
                }
                break;
            
            default:
                // NOTHING
                break;
        }
    }

    /**
     * Handle workflow enter event.
     */
    public function onEnter($event) {
        \Log::debug("OrderSendEmailEventSubscriber@onEnter");
    }
}