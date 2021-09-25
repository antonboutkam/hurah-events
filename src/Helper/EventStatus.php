<?php

namespace Hurah\Event\Helper;

class EventStatus
{

    const NO_EVENT_FILE = 0;
    const NO_LISTENER_FOUND = 0;

    private int $status;

    public function __construct(int $eventStatus)
    {
        $this->status = $eventStatus;
    }

    public function getStatus():int
    {
        return $this->status;
    }

}