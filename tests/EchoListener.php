<?php

namespace Nymfonya\Component\Pubsub\Tests;

use Nymfonya\Component\Pubsub\ListenerAbstract;
use Nymfonya\Component\Pubsub\ListenerInterface;
use Nymfonya\Component\Pubsub\EventInterface;

/**
 * dumb echo listener
 */
class EchoListener extends ListenerAbstract implements ListenerInterface
{

    /**
     * publish
     *
     * @param EventInterface $event
     * @return void
     */
    public function publish(EventInterface $event)
    {
        echo "{$event->getResourceName()} published a {$event->getEventName()}\n";
    }
}
