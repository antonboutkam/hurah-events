<?php

namespace Hurah\Event;

use Hurah\Types\Exception\NullPointerException;
use function array_pop;
use function explode;

class EventType
{

    private array $eventTypeComponents;
    public function __construct(string...$eventTypeComponents)
    {
        $this->eventTypeComponents = $eventTypeComponents;
    }
    public static function fromString(string $eventType):EventType
    {
        $components = explode('/', $eventType);
        return new self(...$components);
    }
    public function asArray():array
    {
        return $this->eventTypeComponents;
    }

    public function isVoid():bool
    {
        return empty($this->eventTypeComponents);
    }

    /**
     * Returns the EventType one level up. So if the event type was "product/stored" this will return the event type
     * "product".
     */
    public function pop():EventType
    {
        if(empty($this->eventTypeComponents))
        {
           throw new NullPointerException("Cannot pop from EventType stack, it is empty");
        }
        array_pop($this->eventTypeComponents);
        return new self(...$this->eventTypeComponents);
    }

}