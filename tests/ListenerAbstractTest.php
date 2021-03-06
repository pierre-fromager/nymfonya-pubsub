<?php

namespace Nymfonya\Component\Pubsub\Tests;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Pubsub\Event;
use Nymfonya\Component\Pubsub\ListenerAbstract;
use Nymfonya\Component\Pubsub\ListenerInterface;
use stdClass;

/**
 * @covers \Nymfonya\Component\Pubsub\ListenerAbstract::<public>
 */
class ListenerAbstractTest extends PFT
{

    const TEST_ENABLE = true;
    const RES_NAME = 'resname';
    const EVENT_NAME = 'eventname';

    /**
     * instance
     *
     * @var ListenerAbstract
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $datas = new stdClass();
        $this->instance = new class (
            self::RES_NAME,
            self::EVENT_NAME,
            $datas
        ) extends ListenerAbstract implements ListenerInterface
        {
        };
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->instance = null;
    }

    /**
     * testPublish
     * @covers Nymfonya\Component\Pubsub\ListenerAbstract::publish
     */
    public function testPublish()
    {
        $this->assertTrue($this->instance instanceof ListenerAbstract);
        $this->assertTrue(
            method_exists($this->instance, 'publish')
        );
        $this->assertTrue(
            method_exists($this->instance, 'hash')
        );
        $implements = class_implements(get_class($this->instance), true);
        $this->assertTrue(
            in_array(ListenerInterface::class, $implements)
        );
        $datas = new stdClass();
        $event = new Event(self::RES_NAME, self::EVENT_NAME, $datas);
        $gen = $this->instance->publish($event);
        //$this->assertTrue(is_string($gen));
        //$this->assertNotEmpty($gen);
    }

    /**
     * testHash
     * @covers Nymfonya\Component\Pubsub\ListenerAbstract::hash
     */
    public function testHash()
    {
        $grn = $this->instance->hash();
        $this->assertTrue(is_string($grn));
        $this->assertNotEmpty($grn);
    }
}
