<?php

namespace Nymfonya\Component\Pubsub;

use Nymfonya\Component\Pubsub\EventInterface;

interface ListenerInterface
{

    const ERR_CLOSURE_ARG_MISSING = 'Listener closure required at least one Event argument';
    const ERR_CLOSURE_ARG_INVALID = 'Listener closure arg type should comply EventInterface';

    /**
     * Accepts an event and does something with it
     *
     * @param EventInterface $event
     */
    public function publish(EventInterface $event);

    /**
     * return listener hash string
     *
     * @return string
     */
    public function hash(): string;
}
