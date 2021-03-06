<?php

declare(strict_types=1);

namespace Nymfonya\Component\Pubsub;

abstract class ListenerAbstract implements ListenerInterface
{
    /**
     * publish
     *
     * @param EventInterface $event
     * @return void
     */
    public function publish(EventInterface $event)
    {
        // do nothing
    }

    /**
     * return listener hash string
     *
     * @return string
     */
    public function hash(): string
    {
        return spl_object_hash($this);
    }
}
