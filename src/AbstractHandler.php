<?php

namespace Hurah\Event;

use Hurah\Event\Helper\HandlerName;
use Hurah\Types\Type\Path;

abstract class AbstractHandler implements HandlerInterface
{

    private EventType $eventType;
    private HandlerName $name;

    public function __construct(HandlerName $name, EventType $eventType, Path $eventRoot)
    {
        $eventRoot->extend($this->eventType->asArray(), $this->name, 'inbox')->makeDir();
        $this->name = $name;
        $this->eventType = $eventType;
    }


    abstract public function handle(Context $context):void;

    public function getType(): EventType
    {
        return $this->eventType;
    }
}