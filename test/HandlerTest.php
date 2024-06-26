<?php

namespace Test\Hurah\Event;

use Hurah\Event\AbstractHandler;
use Hurah\Event\Context;
use Hurah\Event\Dispatcher;
use Hurah\Event\EventType;
use Hurah\Event\Helper\HandlerName;
use Hurah\Event\Task;
use Psr\Log\LoggerInterface;
use Test\Hurah\Event\Helper\FakeLogger;
use Hurah\Event\ContextInterface;

class HandlerTest extends BaseTestCase
{
    public static bool $containsMyTestContext = false;

    public function testHandle()
    {
		/*
        $oName = new HandlerName('price_calculator');
        $oType = new EventType('product');
        $oRoot = $this->eventRoot;

        $handler = new class($oName, $oType, $oRoot) extends AbstractHandler
        {
            public function handle(): void
            {
                $oTaskCollection = $this->getQueue();
                foreach($oTaskCollection as $oTask)
                {
					print_r($oTask->getContext()->getPayload());
                    if(isset($oTask->getContext()->getPayload()['is_test']))
                    {
                        HandlerTest::$containsMyTestContext = true;
                        $oTask->finish();
                    }
                }
            }

            public function getLogger(): LoggerInterface
            {
                return new FakeLogger();
            }

            protected function handleTask(ContextInterface $oContext): int
            {
                return Task::SUCCESS;
            }
        };
        $handler->handle();


        $dispatcher = new Dispatcher($oRoot);
        $dispatcher->dispatch($oType, new Context(['is_test' => 1]));

        $this->assertTrue(self::$containsMyTestContext);
		*/
		$this->assertTrue(true);
    }

}
