<?php

namespace Nymfonya\Component\Pubsub\Tests;

use stdClass;
use ReflectionClass;
use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Pubsub\Dispatcher;
use Nymfonya\Component\Pubsub\Event;
use Nymfonya\Component\Pubsub\EventInterface;
use Nymfonya\Component\Pubsub\ListenerInterface;
use Nymfonya\Component\Pubsub\Tests\EchoListener;

/**
 * @covers \Nymfonya\Component\Pubsub\Dispatcher::<public>
 */
class DispatcherTest extends PFT
{

    const TEST_ENABLE = true;
    const RES_NAME = 'resname';
    const EVENT_NAME = 'eventname';

    /**
     * instance
     *
     * @var Dispatcher
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
        $this->instance = new Dispatcher();
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
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new ReflectionClass(Dispatcher::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testSubscribe
     * @covers Nymfonya\Component\Pubsub\Dispatcher::subscribe
     * @covers Nymfonya\Component\Pubsub\Dispatcher::unsubscribe
     */
    public function testSubscribe()
    {
        $hashSub = $this->instance->subscribe(
            new EchoListener(),
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_string($hashSub));
        $this->assertNotEmpty($hashSub);
        $unsubRes = $this->instance->unsubscribe(
            $hashSub,
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_bool($unsubRes));
        $this->assertTrue($unsubRes);
    }

    /**
     * testUnsubscribeBad
     * @covers Nymfonya\Component\Pubsub\Dispatcher::unsubscribe
     */
    public function testUnsubscribeBad()
    {
        $unsubBad = $this->instance->unsubscribe(
            'badhash',
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_bool($unsubBad));
        $this->assertFalse($unsubBad);
    }

    /**
     * testSubscribeClosure
     * @covers Nymfonya\Component\Pubsub\Dispatcher::subscribeClosure
     * @covers Nymfonya\Component\Pubsub\Dispatcher::unsubscribe
     */
    public function testSubscribeClosure()
    {
        $hashSubClo = $this->instance->subscribeClosure(
            function (EventInterface $event) {
                echo $event->getEventName() . "\n";
                echo $event->getResourceName() . "\n";
            },
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_string($hashSubClo));
        $this->assertNotEmpty($hashSubClo);
        $unsubRes = $this->instance->unsubscribe(
            $hashSubClo,
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertTrue(is_bool($unsubRes));
        $this->assertTrue($unsubRes);
    }

    /**
     * testPublish
     * @covers Nymfonya\Component\Pubsub\Dispatcher::subscribeClosure
     * @covers Nymfonya\Component\Pubsub\Dispatcher::publish
     */
    public function testPublish()
    {
        // names closure sub
        $hClosureNames = $this->instance->subscribeClosure(
            function (EventInterface $event) {
                $eventDatas = $event->getDatas();
                $eventDatas->firstname = 'first name';
                $eventDatas->lastname = 'last name';
            },
            self::RES_NAME,
            self::EVENT_NAME
        );
        // birthday closure sub
        $hClosureBirthday = $this->instance->subscribeClosure(
            function (EventInterface $event) {
                $eventDatas = $event->getDatas();
                $eventDatas->birthday = '1971/10/08';
            },
            self::RES_NAME,
            self::EVENT_NAME
        );
        $this->assertNotEquals($hClosureNames, $hClosureBirthday);
        $datas = new stdClass();
        $this->assertObjectNotHasAttribute('firstname', $datas);
        $this->assertObjectNotHasAttribute('lastname', $datas);
        $this->assertObjectNotHasAttribute('birthday', $datas);
        $eventNamesBirthday = new Event(
            self::RES_NAME,
            self::EVENT_NAME,
            $datas
        );
        $publishRes = $this->instance->publish($eventNamesBirthday);
        $this->assertTrue($publishRes instanceof Dispatcher);
        $this->assertObjectHasAttribute('firstname', $datas);
        $this->assertNotEmpty($datas->firstname);
        $this->assertEquals($datas->firstname, 'first name');
        $this->assertObjectHasAttribute('lastname', $datas);
        $this->assertNotEmpty($datas->lastname);
        $this->assertEquals($datas->lastname, 'last name');
        $this->assertObjectHasAttribute('birthday', $datas);
        $this->assertNotEmpty($datas->birthday);
        $this->assertEquals($datas->birthday, '1971/10/08');
    }

    /**
     * testDispatchAllHandlers
     * @covers Nymfonya\Component\Pubsub\Dispatcher::subscribeClosure
     * @covers Nymfonya\Component\Pubsub\Dispatcher::dispatchAllHandlers
     */
    public function testDispatchAllHandlers()
    {
        $any = Dispatcher::ANY;
        $clo = function (EventInterface $event) {
            $datas = $event->getDatas();
            $datas->toAll = 'done';
        };
        $datas = new stdClass();
        $this->assertObjectNotHasAttribute('toAll', $datas);
        $this->instance->subscribeClosure($clo);
        $event = new Event($any, $any, $datas);
        $dah = self::getMethod('dispatchAllHandlers')->invokeArgs(
            $this->instance,
            [$event]
        );
        $this->assertTrue($dah instanceof Dispatcher);
        $this->assertObjectHasAttribute('toAll', $datas);
        $this->assertEquals('done', $datas->toAll);
    }

    /**
     * testDispatchResourcedHandlers
     * @covers Nymfonya\Component\Pubsub\Dispatcher::subscribeClosure
     * @covers Nymfonya\Component\Pubsub\Dispatcher::dispatchResourcedHandlers
     */
    public function testDispatchResourcedHandlers()
    {
        $any = Dispatcher::ANY;
        $publisherResource = 'kernel.request.pre';
        $clo = function (EventInterface $event) {
            $datas = $event->getDatas();
            $datas->toHandler = 'done';
            $datas->publisher = $event->getResourceName();
            $datas->eventName = $event->getEventName();
        };
        $datas = new stdClass();
        $this->assertObjectNotHasAttribute('toHandler', $datas);
        $this->instance->subscribeClosure($clo, $publisherResource);
        $event = new Event($publisherResource, $any, $datas);
        $dah = self::getMethod('dispatchResourcedHandlers')
            ->invokeArgs(
                $this->instance,
                [$publisherResource, $event]
            );
        $this->instance->publish($event);
        $this->assertTrue($dah instanceof Dispatcher);
        $this->assertObjectHasAttribute('toHandler', $datas);
        $this->assertEquals('done', $datas->toHandler);
        $this->assertObjectHasAttribute('publisher', $datas);
        $this->assertEquals($publisherResource, $datas->publisher);
        $this->assertObjectHasAttribute('eventName', $datas);
        $this->assertEquals($any, $datas->eventName);
    }

    /**
     * testDispatchResourcedEventedHandlers
     * @covers Nymfonya\Component\Pubsub\Dispatcher::subscribeClosure
     * @covers Nymfonya\Component\Pubsub\Dispatcher::dispatchResourcedEventedHandlers
     */
    public function testDispatchResourcedEventedHandlers()
    {
        $publisherResource = 'kernel.request';
        $eventName = 'pre';
        $clo = function (EventInterface $event) {
            $datas = $event->getDatas();
            $datas->toHandler = 'done';
            $datas->publisher = $event->getResourceName();
            $datas->eventName = $event->getEventName();
        };
        $datas = new stdClass();
        $this->assertObjectNotHasAttribute('toHandler', $datas);
        $this->instance->subscribeClosure(
            $clo,
            $publisherResource,
            $eventName
        );
        $event = new Event($publisherResource, $eventName, $datas);
        $dah = self::getMethod('dispatchResourcedEventedHandlers')
            ->invokeArgs(
                $this->instance,
                [$publisherResource, $eventName, $event]
            );
        $this->instance->publish($event);
        $this->assertTrue($dah instanceof Dispatcher);
        $this->assertObjectHasAttribute('toHandler', $datas);
        $this->assertEquals('done', $datas->toHandler);
        $this->assertObjectHasAttribute('publisher', $datas);
        $this->assertEquals($publisherResource, $datas->publisher);
        $this->assertObjectHasAttribute('eventName', $datas);
        $this->assertEquals($eventName, $datas->eventName);
    }
}
