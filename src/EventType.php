<?php

namespace Hurah\Event;

class EventType
{

    private array $eventTypeComponents;
    public function __construct(string...$eventTypeComponents)
    {
        $this->eventTypeComponents = $eventTypeComponents;
    }
    public function asArray():array
    {
        return $this->eventTypeComponents;
    }

}