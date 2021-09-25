<?php

namespace Hurah\Event;

use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\Regex;

class Receiver
{
    private Path $eventRoot;
    private HandlerCollection $handlers;

    /**
     * @param string $eventRoot
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $eventRoot)
    {
        $this->handlers = new HandlerCollection();
        $this->eventRoot = new Path($eventRoot);
    }
    public function onEvent(string $path)
    {
        $oPath = new Path($path);
        if(!$oPath->matches(new Regex('/inbox\/[a-zA-z0-9-_]+$/')))
        {
            return;
        }


        echo $oPath;
    }

    public function addHandler(AbstractHandler $handler)
    {
        $this->eventRoot->extend($handler->getType())->makeDir();
        $this->handlers->add($handler);
    }

}