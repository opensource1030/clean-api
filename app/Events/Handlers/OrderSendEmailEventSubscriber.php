<?php

namespace WA\Events\Handlers;

class OrderSendEmailEventSubscriber
{
    /**
     * Handle workflow guard events.
     */
    public function onGuard(\Brexis\LaravelWorkflow\Events\GuardEvent $event) {
        \Log::debug("onGuard");
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
        \Log::debug("onLeave");
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
                        event(new \WA\Events\Handlers\SendEVEmail($order));
                        event(new \WA\Events\Handlers\SendUsersEmail($order));
                        break;
                    case 'accept':
                        # code...
                        break;
                    case 'deny':
                        # code...
                        break;
                    case 'send':
                        # code...
                        break;
                    
                    default:
                        # code...
                        break;
                }
                break;
            
            default:
                # code...
                break;
        }

        //var_dump($event->getOriginalEvent()->getMarking());
        //var_dump($event->getOriginalEvent()->getTransition());
        
        
        //var_dump($nameTransition);
        //var_dump($nameWorkflow);
        //var_dump($event->getOriginalEvent()->getSubject());
        //var_dump($event->getOriginalEvent()->getWorkflowName());
        
        //dd("onTransition");
        //\Log::debug("onTransition");
    }

    /**
     * Handle workflow enter event.
     */
    public function onEnter($event) {
        \Log::debug("onEnter");
    }
}