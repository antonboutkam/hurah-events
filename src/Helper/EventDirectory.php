<?php

namespace Hurah\Event\Helper;

use Hurah\Event\EventType;
use Hurah\Types\Type\DirectoryIterator;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;

class EventDirectory
{
    private Path $eventTypeRoot;

    public function __construct(Path $eventRoot, EventType $eventType)
    {
        $this->eventTypeRoot = $eventRoot->extend($eventType->asArray())->makeDir();
    }

    public function getListeners():PathCollection
    {
        return $this->eventTypeRoot->getDirectoryIterator()->toPathCollection(DirectoryIterator::DIR_ONLY);
    }

}