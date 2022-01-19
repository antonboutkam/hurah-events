<?php

namespace Hurah\Event;

use Hurah\Event\Helper\DeliveryService;
use Hurah\Event\Helper\FileStructureHelper;
use Hurah\Types\Type\Path;
use function var_dump;

class Dispatcher
{
    private Path $eventRoot;
    public function __construct(string $eventRoot)
    {
        $this->eventRoot = new Path($eventRoot);
    }

    public function dispatch(EventType $eventType, Context $data)
    {
        $fileStructureHelper = new FileStructureHelper($this->eventRoot);

        foreach($fileStructureHelper->findEventListeners($eventType) as $listenedDirectoryPath)
        {
            $this->deliver($listenedDirectoryPath, $data);
        }
    }
    private function deliver(Path $oHandlerDirectory, Context $data)
    {
        $oEndpoint = new DeliveryService($oHandlerDirectory);
        $oEndpoint->writeToInbox($data);
    }

}