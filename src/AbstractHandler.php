<?php

namespace Hurah\Event;

use Hurah\Event\Helper\HandlerName;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;
use Hurah\Types\Type\Regex;

abstract class AbstractHandler implements HandlerInterface
{
    private EventType $eventType;
    private Path $inbox;

    public function __construct(HandlerName $name, EventType $eventType, Path $eventRoot)
    {
        $this->inbox = $eventRoot->extend($eventType->asArray(), $name . '_listener', 'inbox')->makeDir();
        $this->name = $name;
        $this->eventType = $eventType;
    }

    private function getInboxFiles():PathCollection
    {
        return $this->inbox->getDirectoryIterator()->toPathCollection();
    }
    /**
     * @return TaskCollection
     * @throws InvalidArgumentException
     */
    public function getQueue(): TaskCollection
    {
        $oFilter = new Regex('/[0-9]+.json$/');

        return TaskCollection::fromPathCollection($this->getInboxFiles()->filter($oFilter));
    }

    abstract public function handle(): void;

    public function getType(): EventType
    {
        return $this->eventType;
    }

}