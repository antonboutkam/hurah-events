<?php

namespace Hurah\Event;

use Hurah\Event\Helper\Endpoint;
use Hurah\Event\Helper\EventDirectory;
use Hurah\Types\Type\Path;

class Dispatcher
{
    private Path $eventRoot;
    public function __construct(string $eventRoot)
    {
        $this->eventRoot = new Path($eventRoot);
    }

    public function dispatch(EventType $eventType, Context $data)
    {
        $fileStructure = new EventDirectory($this->eventRoot, $eventType);

        foreach($fileStructure->getListeners() as $listener)
        {
            $this->deliver($listener, $data);
        }
    }
    private function deliver(Path $listener, Context $data)
    {
        $oEndpoint = new Endpoint($listener);
        $oEndpoint->inbox($data);

        $listener->extend('inbox')->write($data);
    }

}