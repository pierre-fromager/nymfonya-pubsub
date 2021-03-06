<?php

declare(strict_types=1);

namespace Nymfonya\Component\Pubsub;

use Closure;
use Nymfonya\Component\Pubsub\ListenerInterface;
use Nymfonya\Component\Pubsub\EventInterface;

interface DispatcherInterface
{

    const ANY = '*';

    /**
     * subscribe
     *
     * @param ListenerInterface $listener
     * @param string $resourceName
     * @param string $event
     * @return string
     */
    public function subscribe(
        ListenerInterface $listener,
        $resourceName = self::ANY,
        $event = self::ANY
    ): string;

    /**
     * subscribeClosure
     *
     * @param Closure $listener
     * @param string $resourceName
     * @param string $event
     * @return string
     */
    public function subscribeClosure(
        Closure $closure,
        $resourceName = self::ANY,
        $event = self::ANY
    ): string;

    /**
     * unsubscribe
     *
     * @param string $hash
     * @param string $resourceName
     * @param string $event
     * @return boolean
     */
    public function unsubscribe(
        string $hash,
        $resourceName = self::ANY,
        $event = self::ANY
    ): bool;

    /**
     * publish
     *
     * @param EventInterface $event
     * @return Dispatcher
     */
    public function publish(EventInterface $event): DispatcherInterface;
}
