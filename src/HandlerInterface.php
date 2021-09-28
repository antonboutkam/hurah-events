<?php

namespace Hurah\Event;

interface HandlerInterface
{
    public function handle():void;
    public function getType():EventType;
}