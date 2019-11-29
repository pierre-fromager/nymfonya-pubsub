<?php

namespace Nymfonya\Component\Pubsub\Tests;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Pubsub\Event;
use Nymfonya\Component\Pubsub\EventInterface;

/**
 * @covers \Nymfonya\Component\Pubsub\Event::<public>
 */
class EventTest extends PFT
{

    const TEST_ENABLE = true;
    const RES_NAME = 'resname';
    const EVENT_NAME = 'eventname';

    /**
     * instance
     *
     * @var Event
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->instance = new Event(self::RES_NAME, self::EVENT_NAME);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
    }

    /**
     * testInstance
     * @covers Nymfonya\Component\Pubsub\Event::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Event);
        $this->assertTrue(
            method_exists($this->instance, 'getEventName')
        );
        $this->assertTrue(
            method_exists($this->instance, 'getResourceName')
        );
        $this->assertTrue(
            method_exists($this->instance, 'getDatas')
        );
        $implements = class_implements(Event::class, true);
        $this->assertTrue(
            in_array(EventInterface::class, $implements)
        );
    }
}
