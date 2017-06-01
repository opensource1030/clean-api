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
class EmailEvent extends \WA\Events\Handlers\BaseHandler
{
    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function sendEmailTo($event)
    {
        dd("HERE");
    }

    /**
     * @param Dispatcher $events
     */
    public function handle(Dispatcher $events)
    {
        $events->listen('sendEmailTo', 'WA\Events\Handlers\EmailEvent@sendEmailTo');
    }
}
