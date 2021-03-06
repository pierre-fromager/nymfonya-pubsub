<?php

declare(strict_types=1);

namespace Nymfonya\Component\Pubsub;

class Event extends EventAbstract
{
    /**
     * @param string $resourceName  name of the publisher
     * @param string $eventName     name of the event
     * @param mixed $data           [OPTIONAL] Additional event data
     */
    public function __construct(
        string $resourceName,
        string $eventName,
        $data = null
    ) {
        parent::__construct($resourceName, $eventName, $data);
    }
}
