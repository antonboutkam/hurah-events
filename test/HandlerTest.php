<?php

namespace Test\Hurah\Event;

use Hurah\Event\AbstractHandler;
use Hurah\Event\Context;
use Hurah\Event\Dispatcher;
use Hurah\Event\EventType;
use Hurah\Event\Helper\HandlerName;

class HandlerTest extends BaseTestCase
{
    public static $containsMyTestContext = false;

    public function testHandle()
    {
        $oName = new HandlerName('price_calculator');
        $oType = new EventType('product');
        $oRoot = $this->eventRoot;

        $dispatcher = new Dispatcher($oRoot);
        $dispatcher->dispatch($oType, new Context(['is_test' => 1]));

        $handler = new class($oName, $oType, $oRoot) extends AbstractHandler
        {
            public function handle(): void
            {
                $oTaskCollection = $this->getQueue();
                foreach($oTaskCollection as $oTask)
                {
                    if(isset($oTask->getContext()->getPayload()['is_test']))
                    {
                        HandlerTest::$containsMyTestContext = true;
                        $oTask->finish();
                    }
                }
            }
        };
        $handler->handle();
        $this->assertTrue(self::$containsMyTestContext);
    }

}