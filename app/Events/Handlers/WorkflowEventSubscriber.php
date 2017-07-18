<?php

namespace WA\Events\Handlers;

class WorkflowEventSubscriber
{
    /**
     * Handle workflow guard events.
     */
    public function onGuard(\Brexis\LaravelWorkflow\Events\GuardEvent $event) {
        \Log::debug("WorkflowEventSubscriber@onGuard");
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
        \Log::debug("WorkflowEventSubscriber@onLeave");
    }

    /**
     * Handle workflow transition event.
     */
    public function onTransition($event) {
        \Log::debug("WorkflowEventSubscriber@onTransition");
        
        $nameWorkflow = $event->getOriginalEvent()->getWorkflowName();
        $nameTransition = $event->getOriginalEvent()->getTransition()->getName();
        $order = $event->getOriginalEvent()->getSubject();
        
        switch ($nameWorkflow) {
            case 'new_order':
                switch ($nameTransition) {
                    case 'create':
                        \Log::debug("OrderCreateTransition@onTransition - create");
                        event(new \WA\Events\OrderCreateTransition($order));
                        break;
                    case 'accept':
                        \Log::debug("WorkflowEventSubscriber@onTransition - accept");
                        event(new \WA\Events\OrderAcceptedTransition($order));
                        break;
                    case 'deny':
                        \Log::debug("WorkflowEventSubscriber@onTransition - deny");
                        event(new \WA\Events\OrderDeniedTransition($order));
                        break;
                    case 'send':
                        \Log::debug("WorkflowEventSubscriber@onTransition - send");
                        event(new \WA\Events\OrderDeliveredTransition($order));
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
        \Log::debug("WorkflowEventSubscriber@onEnter");
    }
}