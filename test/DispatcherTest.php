<?php

namespace Test\Hurah\Event;

use Composer\Script\Event;
use Hurah\Event\AbstractHandler;
use Hurah\Event\Context;
use Hurah\Event\Dispatcher;
use Hurah\Event\EventType;
use Hurah\Event\Receiver;
use Hurah\Types\Type\Path;
use PHPUnit\Framework\TestCase;
use function dirname;

class DispatcherTest extends TestCase
{
    public function testDispatch()
    {
        $oEventRoot = Path::make(dirname(__DIR__, 1), 'data')->makeDir();
        $oEventType = new EventType('product', 'created');

        $oHandler = new class($oEventType) extends AbstractHandler
        {

            public function handle(Context $context): void
            {
                exit('testing');
            }

        };



        $oListener = new Receiver($oEventRoot);
        $oListener->addHandler($oHandler);

        $oDispatcher = new Dispatcher($oEventRoot);
        $oContext = new Context(['product_id' => 1]);

        $oDispatcher->dispatch($oEventType, $oContext);


    }

}
