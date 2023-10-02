<?php

namespace Hurah\Event;

use Hurah\Event\Helper\DeliveryService;
use Hurah\Event\Helper\FileStructureHelper;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use function var_dump;

class Dispatcher
{
    private Path $eventRoot;
    public function __construct(string $eventRoot)
    {
        $this->eventRoot = new Path($eventRoot);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function dispatch(EventType $eventType, ContextInterface $data)
    {
        $fileStructureHelper = new FileStructureHelper($this->eventRoot);

        foreach($fileStructureHelper->findEventListeners($eventType) as $listenedDirectoryPath)
        {
            $this->deliver($listenedDirectoryPath, $data);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function deliver(Path $oHandlerDirectory, ContextInterface $data)
    {
        $oEndpoint = new DeliveryService($oHandlerDirectory);
        $oEndpoint->writeToInbox($data);
    }

}
